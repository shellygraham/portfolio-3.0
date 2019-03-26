<?php

/*
*  rest_api_init
*/

add_action( 'rest_api_init', function () {
   $my_namespace = 'alm-filters';
   $my_endpoint = '/save';
   register_rest_route( $my_namespace, $my_endpoint,
      array(
         'methods' => 'POST',
         'callback' => 'save_filter',
      )
   );
});



/*
*  save_filter
*  Save the filter data
*
*  @param $request      $_POST
*  @return $response    json
*  @since 1.0

*/

function save_filter( WP_REST_Request $request ) {

	error_reporting(E_ALL|E_STRICT);

	// Get contents of request and convert to array
	$data = json_decode($request->get_body(), true);	

	$options = json_decode($data['options']);
	$filters = json_decode($data['filters']);
	
	$filter_array = [];
	$filter_array['id'] = '';

	if($filters){

		// Loop options and build options array
		foreach($options as $key => $value){

   		// Get the ID
   		$id = isset($options[$key]->id) ? strtolower($options[$key]->id) : '';

   		// Get option from DB
   		$option = get_option( 'alm_filter_'. $id);

   		// Set ID and Style
			$filter_array['id'] = $id;
			$filter_array['style'] = isset($options[$key]->style) ? $options[$key]->style : '';

			// Only set button_text if style === change
			if($filter_array['style'] === 'button'){
				$filter_array['button_text'] = isset($options[$key]->button_text) ? $options[$key]->button_text : '';
			}

			$timestamp = current_time( 'timestamp' ); // Get current time

			// Created Date
			if(!$option){ // if filter doesn't yet exist
				$filter_array['date_created'] = $timestamp;
			} else {

   			// Get current filter for date created attribute
   			$filter_option = unserialize($option);
   			if(!isset($filter_option['date_created'])){
      			// If it doesn't exist, created it.
      			$filter_array['date_created'] = $timestamp;
   			} else {
      			// Set it back to original value
      			$filter_array['date_created'] = $filter_option['date_created'];
   			}
			}

			// Update modified date
			$filter_array['date_modified'] = $timestamp;

		}

		$filter_array['filters'] = [];

		// convert $filters to array from stdClass Object
		$filters = json_decode(json_encode($filters), true);

		// Loop each as a $filter
		foreach($filters as $filter){

   		// confirm atleast a key and field_type are set before pushing into array
			if($filter['key'] && $filter['field_type']){

   			// convert $filter to array from stdClass Object
   			$array = json_decode(json_encode($filter), true);

   			// Remove items from the array if empty

   			// Taxonomy
   			if(isset($array['taxonomy']) && $array['taxonomy'] === ''){
	   			unset($array['taxonomy']);
	   			unset($array['taxonomy_operator']);
   			}
   			// Clear Custom Field value if $filter['key'] !== 'meta'
   			if($filter['key'] !== 'taxonomy'){
	   			unset($array['taxonomy']);
	   			unset($array['taxonomy_operator']);
   			}

   			// Custom Fields
   			if(isset($array['meta_key']) && $array['meta_key'] === ''){
	   			unset($array['meta_key']);
	   			unset($array['meta_operator']);
	   			unset($array['meta_type']);
   			}
   			// Clear Custom Field value if $filter['key'] !== 'meta'
   			if($filter['key'] !== 'meta'){
	   			unset($array['meta_key']);
	   			unset($array['meta_operator']);
	   			unset($array['meta_type']);
   			}

   			// role
   			if(isset($array['author_role']) && $array['author_role'] === ''){
	   			unset($array['author_role']);
	   		}

   			// exclude
   			if(isset($array['exclude']) && $array['exclude'] === ''){
	   			unset($array['exclude']);
	   		}

   			// selected value
   			if((isset($array['selected_value']) && $array['selected_value'] === '') || $filter['field_type'] === 'text' || $filter['field_type'] === 'checkbox'){
	   			unset($array['selected_value']);
	   		}

   			// default value
   			if((isset($array['default_value']) && $array['default_value'] === '')){
	   			unset($array['default_value']);
	   		}

	   		// Custom Values
   			if((isset($array['values']) && $array['values'] === '') || empty($array['values']) || $filter['field_type'] === 'text'){
	   			unset($array['values']);
   			}

   			// Label
   			if(isset($array['label']) && $array['label'] === ''){
	   			unset($array['label']);
	   		}

   			// Title
   			if(isset($array['title']) && $array['title'] === ''){
	   			unset($array['title']);
	   		}

   			// Button Label
   			if((isset($array['button_label']) && $array['button_label'] === '') || $filter['field_type'] !== 'text'){
	   			unset($array['button_label']);
	   		}

   			// Placeholder
   			if(isset($array['placeholder']) && $array['placeholder'] === ''){
	   			unset($array['placeholder']);
	   		}

	   		unset($array['order']);
	   		unset($array['uniqueid']);

   			array_push($filter_array['filters'], $array);
   			
			}
			
		}

	}


	// Create the response obj.
	if(count($filter_array['filters']) > 0 && $filter_array['id'] !== ''){ // If array is larger than just $options and ID is set

   	update_option( 'alm_filter_'. $filter_array['id'], serialize($filter_array) ); // Update/Create option on success

		$response = array(
			'success' => true,
			'msg'     => __('Filter saved successfully', 'ajax-load-more-filters '),
			'code'    => json_encode($filter_array, JSON_PRETTY_PRINT)
		);

	} else {

		$response = array(
			'success' => false,
			'msg'     => __('You need to add some filter criteria.', 'ajax-load-more-filters '),
			'code'    => ''
		);

	}

	wp_send_json($response); // Send JSON response

}
