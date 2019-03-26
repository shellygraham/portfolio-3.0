<?php
/*
Plugin Name: Ajax Load More: Filters
Plugin URI: https://connekthq.com/plugins/ajax-load-more/filters/
Description: Ajax Load More add-on to build and manage Ajax filters.
Author: Darren Cooney
Twitter: @KaptonKaos
Author URI: https://connekthq.com
Version: 1.6.4
License: GPL
Copyright: Darren Cooney & Connekt Media
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('ALM_FILTERS_VERSION', '1.6.4');
define('ALM_FILTERS_RELEASE', 'February 4, 2019');
define('ALM_FILTERS_PATH', plugin_dir_path(__FILE__));
define('ALM_FILTERS_URL', plugins_url('', __FILE__));
define('ALM_FILTERS_ADMIN_URL', plugins_url('admin/', __FILE__));
define('ALM_FILTERS_SLUG', 'ajax-load-more-filters');
define('ALM_FILTERS_BASE_URL', get_admin_url() .'admin.php?page='. ALM_FILTERS_SLUG);
define('ALM_FILTERS_PREFIX', 'alm_filter_');



/*
 *  alm_filters_install
 *  Install the Users add-on
 *
 *  @since 1.0
 */

register_activation_hook( __FILE__, 'alm_filters_install' );
function alm_filters_install() {
   if(!is_plugin_active('ajax-load-more/ajax-load-more.php')){	//if Ajax Load More is activated
   	die('You must install and activate <a href="https://wordpress.org/plugins/ajax-load-more/">Ajax Load More</a> before installing the Ajax Load More Filters add-on.');
	}
}


if( !class_exists('ALMFilters') ):

   class ALMFilters{
	   
	   // vars
		var $notices = array();

	   // Count filters
	   static $counter = 0;

	   // An array of filter operators used with each filter
	   // Store tax operator, meta operator and type in an array as they are not passed in the querystring.
	   static $alm_filters_key_operators = [];


   	function __construct(){

   		add_action( 'alm_filters_installed', array(&$this, 'alm_filters_installed') );
   		add_action( 'wp_enqueue_scripts', array(&$this, 'alm_filters_enqueue_scripts') );
   		add_action('admin_enqueue_scripts', array(&$this, 'alm_filters_admin_enqueue_scripts'));
   		add_action( 'ajax_load_more_filters_', array(&$this, 'ajax_load_more_filters_') );
   		add_shortcode( 'ajax_load_more_filters', array(&$this, 'alm_filters_shortcode' ));
   		add_action( 'alm_filters_settings', array(&$this, 'alm_filters_settings' ));
   		add_filter( 'alm_filters_shortcode_params', array(&$this, 'alm_filters_shortcode_params'), 10, 5 );
   	   add_filter( 'alm_filters_preloaded_args', array(&$this, 'alm_filters_preloaded_args' ), 10, 1 );
   		add_filter( 'alm_filters_reveal_open', array(&$this, 'alm_filters_reveal_open'), 10, 3 );
   		add_filter( 'alm_filters_reveal_close', array(&$this, 'alm_filters_reveal_close'), 10, 2);
   		
   		add_action( 'admin_init', array(&$this, 'alm_filters_export'));
   		add_action( 'admin_init', array(&$this, 'alm_filters_import'));
   		add_action( 'admin_init', array(&$this, 'alm_filters_deleted'));
   		add_action( 'admin_init', array(&$this, 'alm_filters_updated'));
   		add_action( 'admin_notices', array(&$this, 'admin_notices'));
   		
   		load_plugin_textdomain( 'ajax-load-more-filters', false, dirname(plugin_basename( __FILE__ )).'/lang/'); //load text domain
         $this->includes();

   	}
   	
   	
   	
   	/*
   	 *  alm_filters_deleted
   	 *  This function will delete a filter from the options table
   	 *
   	 *  @type	function
		 *  @since	1.5
		 *
		 *  @param	n/a
		 *  @return	n/a
   	 */
   	function alm_filters_deleted(){
	   	if(isset($_GET['delete_filter'])) {
				$deleted_filter = $_GET['delete_filter'];
				// Confirm option exists
				if(!empty(get_option(ALM_FILTERS_PREFIX .$_GET['delete_filter']))){
					delete_option(ALM_FILTERS_PREFIX .$_GET['delete_filter']);
					$message = '<strong>'. $deleted_filter .'</strong> '. __('filter was successfully deleted', 'ajax-load-more-filters');
					$this->alm_filters_add_admin_notice($message, 'ajax-load-more-filters');
				}
			}
   	}
   	
   	
   	/*
   	 *  alm_filters_add_admin_notice
   	 *  This function will add admin notices
   	 *
   	 *  @type	function
		 *  @since	1.5
		 *
		 *  @param	$text string
		 *  @param	$class string
		 *  @param	$wrap string
		 *  @return	add_notice()
   	 */
		function alm_filters_add_admin_notice( $text, $class = '', $wrap = 'p' ) {
			return $this->add_notice($text, $class, $wrap);			
		}
		
		
		
		/*
   	 *  add_notice
   	 *  This function will add admin notices to the $notices array
   	 *
   	 *  @type	function
		 *  @since	1.5
		 *
		 *  @param	$text string
		 *  @param	$class string
		 *  @param	$wrap string
		 *  @return	n/a
   	 */
   	function add_notice( $text = '', $class = '', $wrap = 'p' ) {	
			// append
			$this->notices[] = array(
				'text'	=> $text,
				'class'	=> 'updated ' . $class,
				'wrap'	=> $wrap
			);			
		}
		
		
		
		/*
   	 *  get_notices
   	 *  This function will return $notices
   	 *
   	 *  @type	function
		 *  @since	1.5
		 *
		 *  @param	n/a
		 *  @return	$notices
   	 */
		function get_notices() {			
			if( empty($this->notices) ) return false; // bail early if no notices			
			return $this->notices;		
		}
		
		
   	
		/*
		 *  admin_notices
		 *  This function will render admin notices
		 *
		 *  @type	function
		 *  @since	1.5
		 *
		 *  @param	n/a
		 *  @return	n/a
		 */
		
		function admin_notices() {
		
			// vars
			$notices = $this->get_notices();
			
			// bail early if no notices
			if( !$notices ) return;
			
			// loop
			foreach( $notices as $notice ) {
				$open = $close = '';				
				if( $notice['wrap'] ) {				
					$open = "<{$notice['wrap']}>";
					$close = "</{$notice['wrap']}>";				
				}				
				?>
				<div class="alm-admin-notice notice is-dismissible <?php echo esc_attr($notice['class']); ?>"><?php echo $open . $notice['text'] . $close; ?></div>
			<?php
			}
		}
   	
   	
   	
   	/*
   	*  alm_filters_updated
   	*  Was a filter updated?
   	*
   	*  @since 1.6
   	*/

   	public function alm_filters_updated(){
	   	if( isset( $_GET["filter_updated"] ) ) {
		   	$this->alm_filters_add_admin_notice('<i class="fa fa-check-square" style="color: #46b450";></i>&nbsp; '.__('Filter successfully updated.', 'ajax-load-more-filters'), 'success');
		   }	   
	   }
   	
   	
   	
   	/*
   	*  alm_filters_export
   	*  Export ALM Filter Groups
   	*
   	*  @since 1.5
   	*/

   	public function alm_filters_export(){
      	
      	if( isset( $_POST["alm_filters_export"] ) ) {
            
            $filename = 'alm-filters';
         	if(!empty($_POST['filter_keys'])){
            	$export_array = [];
               foreach($_POST['filter_keys'] as $name){
                  $option = get_option($name);
                  $export_array[] = unserialize($option);
                  $filename .= '['. ALMFilters::alm_filters_replace_string($name) .']';
               }
         	
	         	$filename = $filename .= '.json';
	      		header( "Content-Description: File Transfer" );
	      		header( "Content-Disposition: attachment; filename={$filename}" );
	      		header( "Content-Type: application/json; charset=utf-8" );      		
	      		
	      		// return
	      		echo json_encode($export_array, JSON_PRETTY_PRINT);
	      		
	      		die();
	      		
      		} else {
	      			      		
            	$this->alm_filters_add_admin_notice(__('No filter groups selected', 'ajax-load-more-filters'), 'error');
            	
      		}
         } 
   	}
   	


   	/*
   	*  alm_filters_import
   	*  Import ALM Filter Groups
   	*
   	*  @since 1.5
   	*/

   	public function alm_filters_import(){
      	
      	if( isset( $_POST["alm_filters_import"] ) ) {
	      	
	      	// vars
				$file = $_FILES['alm_import_file'];
				
				if($file){	
	      	
					// validate type
					if( pathinfo($file['name'], PATHINFO_EXTENSION) !== 'json' ) {
						$this->alm_filters_add_admin_notice(__('Incorrect file type', 'ajax-load-more-filters'), 'error');
						return;
					}		
			
					// read file
					$json = file_get_contents( $file['tmp_name'] );
					
					// decode json
					$json = json_decode($json, true);			
					
					// validate json
					if( empty($json) ) {
						$this->alm_filters_add_admin_notice(__('Import file empty', 'ajax-load-more-filters'), 'error');
						return;				
					}	
					
					// Incorrect JSON format
					if(!is_array($json)){
						$this->alm_filters_add_admin_notice(__('JSON file formatted incorrectly', 'ajax-load-more-filters'), 'error');
						return;	
					}
					
					
					// Loop all filters
					$count = 0;
					$import_string = '';
					foreach($json as $filter){
						
						if(!isset($filter['id'])){
							$this->alm_filters_add_admin_notice(__('JSON file formatted incorrectly', 'ajax-load-more-filters'), 'error');
							break;							
						}
						$id = $filter['id'];
						
						if(!isset($filter['style'])) {							
							$this->alm_filters_add_admin_notice(__('JSON file formatted incorrectly', 'ajax-load-more-filters'), 'error');
							break;
						}
						$style = $filter['style'];
						
						if(!isset($filter['filters'])){
							$this->alm_filters_add_admin_notice(__('JSON file formatted incorrectly', 'ajax-load-more-filters'), 'error');
							break;	
						}
						
						$filters = $filter['filters'];
						
						if($filters && $id && $style){
							
							$filter = serialize($filter);
							update_option(ALM_FILTERS_PREFIX . $id, $filter);
							
							$import_string .= ($count > 0) ? ', ' : '';
							$import_string .= '<a href="'. ALM_FILTERS_BASE_URL .'&filter='. $id .'"><strong>'. $id .'</strong></a>';	
													
							$count++;
							
						}
						
					}
					
					if($count > 0){
						$this->alm_filters_add_admin_notice($import_string . __(' successfully imported', 'ajax-load-more-filters'));
					}					
					
						   
				} else {
					
					// Error
					$this->alm_filters_add_admin_notice(__('An error has occurred', 'ajax-load-more-filters'), 'error');
					
				}	
         }
   	}
   	


   	/*
   	*  includes
   	*  Include these files for REST API integration
   	*
   	*  @since 1.0
   	*/

   	public function includes(){      	

		   include_once('admin/api/save.php');
         include_once('admin/api/renderfilter.php');

      }



   	/*
   	*  alm_filters_enqueue_scripts
   	*  Enqueue filter JS and CSS
   	*
   	*  @since 1.0
   	*/

   	public function alm_filters_enqueue_scripts(){

      	// Get ALM Options
   		$options = get_option( 'alm_settings' );

   		// JS and Localization
   		wp_register_script( 'ajax-load-more-filters', plugins_url( '/dist/js/filters.js', __FILE__ ), 'ajax-load-more',  ALM_FILTERS_VERSION, true );

   		// Enqueue CSS
   		if(!alm_do_inline_css('_alm_inline_css') && !alm_css_disabled('_alm_filters_disable_css')){ // Not inline or disabled

      		$file = ALM_FILTERS_URL.'/dist/css/styles.css';
         	if(class_exists('ALM_ENQUEUE')){
            	ALM_ENQUEUE::alm_enqueue_css(ALM_FILTERS_SLUG, $file);
         	}

   		}

   	}




   	/*
   	*  alm_filters_admin_enqueue_scripts
   	*  Enqueue our fitlers frontend scripts in the admin
   	*
   	*  @since 1.0
   	*/

   	function alm_filters_admin_enqueue_scripts(){
	   	$screen = get_current_screen();
	   	// Only load on settings page
	   	if($screen->base === 'toplevel_page_ajax-load-more'){
      		wp_enqueue_style( 'alm-filters-frontend', ALM_FILTERS_URL. '/dist/css/styles.css', '');
      	}
   	}



      /*
		*  alm_filters_shortcode
		*  The Ajax Load More Filter Shortcode
		*  [ajax_load_more_filters id="{id}" target="{target}"]
		*
		*  @since 1.0
		*/

		public static function alm_filters_shortcode( $atts ) {

			$args = shortcode_atts(
				array(
					'id' => '',
					'target' => ''
				),
				$atts
         );

         $id = esc_attr($args['id']);
         $target = esc_attr($args['target']);
         $filter = get_option(ALM_FILTERS_PREFIX . $id); // Get the option

         if($filter && $target){
	         $filter_array = unserialize($filter);
	         return ALMFilters::init($filter_array, $target);
         }

      }



   	public static function init($filters, $target){

	   	$options = get_option( 'alm_settings' );

	   	self::$counter++;

	   	// Array to store initial state of app.
	   	// This is required for taxonomy and meta_query as operators and types are not accessibile in the URL.

	   	// Enqueue Filters JS
      	wp_enqueue_script( 'ajax-load-more-filters' );

			// Inline Filters CSS
			if(class_exists('ALM_ENQUEUE')){
				if( !is_admin() && alm_do_inline_css('_alm_inline_css') && !alm_css_disabled('_alm_filters_disable_css') && self::$counter === 1 ){
					$file = ALM_FILTERS_PATH.'/dist/css/styles.css';
					echo ALM_ENQUEUE::alm_inline_css(ALM_FILTERS_SLUG, $file, ALM_FILTERS_URL);
				}
			}

			// Parse the URL
			$queryString = self::alm_filters_parse_url();

			$output = '';
			$filterCount = 0;
			$container_element = 'div';

			if($filters['filters']){

				$options_obj = array(
		         'target' => (isset($target)) ? esc_attr($target) : '',
		         'id' => (isset($filters['id'])) ? esc_attr($filters['id']) : '',
		         'style' => (isset($filters['style'])) ? esc_attr($filters['style']) : 'change',
		         'button_text' => (isset($filters['button_text']) && !empty($filters['button_text'])) ? $filters['button_text'] : apply_filters('alm_filters_button_text', __('Submit', 'ajax-load-more-filters')),
		      );

		      // Get color
				$filters_color = '';
				if(isset($options['_alm_filters_color'])){
					$filters_color = ' filters-'.$options['_alm_filters_color'];
				}

				$output .= '<'.$container_element.' class="alm-filters alm-filters-container'. $filters_color .'" id="alm-filters-'. $options_obj['id'] .'" data-target="'. $options_obj['target'] .'" data-style="'. $options_obj['style'] .'">';

					foreach($filters['filters'] as $f){

						$filterCount++;

						$obj = array(
				         'key' => (isset($f['key'])) ? self::alm_filters_replace_underscore(esc_attr($f['key'])) : '',
				         'field_type' => (isset($f['field_type'])) ? esc_attr($f['field_type']) : '',
				         'taxonomy' => (isset($f['taxonomy'])) ? esc_attr($f['taxonomy']) : '',
				         'taxonomy_operator' => (isset($f['taxonomy_operator'])) ? self::alm_filters_replace_underscore(esc_attr($f['taxonomy_operator'])) : 'IN',
				         'meta_key' => (isset($f['meta_key'])) ? esc_attr($f['meta_key']) : '',
				         'meta_operator' => (isset($f['meta_operator'])) ? self::alm_filters_replace_underscore(esc_attr($f['meta_operator'])) : 'IN',
				         'meta_type' => (isset($f['meta_type'])) ? self::alm_filters_replace_underscore(esc_attr($f['meta_type'])) : 'CHAR',
				         'exclude' => (isset($f['exclude'])) ? esc_attr($f['exclude']) : '',
				         'author_role' => (isset($f['author_role'])) ? esc_attr($f['author_role']) : '',
				         'values' => (isset($f['values'])) ? $f['values'] : '',
				         'title' => (isset($f['title'])) ? esc_attr($f['title']) : '',
			            'label' => (isset($f['label'])) ? esc_attr($f['label']) : '',
			            'button_label' => (isset($f['button_label'])) ? $f['button_label'] : '',
			            'placeholder' => (isset($f['placeholder'])) ? esc_attr($f['placeholder']) : '',
				         'count' => $filterCount,
				      );
				      
                  $filter_key = $obj['key'];
                  // Set `$filter_key` to taxonomy/meta_key	value for core filters                        
				      $filter_key = ($obj['key'] === 'taxonomy') ? $obj['taxonomy'] : $obj['key']; // Convert $key to $taxonomy value
                  $filter_key = ($obj['key'] === 'meta') ? $obj['meta_key'] : $filter_key; // Convert $key to $meta_key value
				      
				      
				      /*
               	*  alm_filters_{id}_{key}_selected
               	*  Set Pre-selected value of element - Core Filter hook
               	*
               	*  @since 1.1.1
               	*/
               	$obj['selected_value'] = (isset($f['selected_value'])) ? esc_attr($f['selected_value']) : '';
				      if(has_filter('alm_filters_'. $options_obj['id'] .'_'. $filter_key .'_selected')){
   				      $obj['selected_value'] = apply_filters('alm_filters_'. $options_obj['id'] .'_'. $filter_key .'_selected', $obj['selected_value']);
				      }				      
				      $default_selected_value = ($obj['selected_value']) ? ' data-selected-value="'. $obj['selected_value'] .'"' : '';
				      
				      				      
				      /*
               	*  alm_filters_{id}_{key}_default
               	*  Set a default/fallback value of element - Core Filter hook
               	*
               	*  @since 1.1.1
               	*/
               	$obj['default_value'] = (isset($f['default_value'])) ? esc_attr($f['default_value']) : '';
				      if(has_filter('alm_filters_'. $options_obj['id'] .'_'. $filter_key .'_default')){
   				      $obj['default_value'] = apply_filters('alm_filters_'. $options_obj['id'] .'_'. $filter_key .'_default', $obj['default_value']);
				      }				      
				      $default_value = ($obj['default_value']) ? ' data-default-value="'. $obj['default_value'] .'"' : '';
				      
				     
				      // Get Taxonomy Values
			         $taxonomy_value = $taxonomy_operator = '';
			         if($obj['taxonomy'] && $obj['taxonomy_operator']){
				         $taxonomy_value = ' data-taxonomy="'. $obj['taxonomy'] .'"';
				         $taxonomy_operator = ' data-taxonomy-operator="'. $obj['taxonomy_operator'] .'"';

				         // Add taxonomy & operator to session
							/* NOTE */ 
							//Deprecate this soon.
				         self::$alm_filters_key_operators[] = array(
					        'taxonomy' => $obj['taxonomy'],
					        'taxonomy_operator' => $obj['taxonomy_operator'],
				         );

			         }
			         

				      // Get Meta Values
			         $meta_value = $meta_operator = $meta_type = '';
			         if($obj['meta_key'] && $obj['meta_operator'] && $obj['meta_type']){
				         $meta_value = ' data-meta-key="'. $obj['meta_key'] .'"';
				         $meta_operator = ' data-meta-compare="'. $obj['meta_operator'] .'"';
				         $meta_type = ' data-meta-type="'. $obj['meta_type'] .'"';

				         // Add meta_key, operator and type to session
				         /* NOTE */ 
				         //Deprecate this soon.
				         self::$alm_filters_key_operators[] = array(
					        'meta_key' => $obj['meta_key'],
					        'meta_operator' => $obj['meta_operator'],
					        'meta_type' => $obj['meta_type'],
				         );

			         }
			         
			         
			         // Convert Search Key for use on WP search page ?s={term}
			         if($obj['key'] === 'search' && is_search()){
							$obj['key'] = 's';
						}
						

			         // Set Author Role
			         $author_role = ($obj['author_role']) ? ' data-author-role="'. $obj['author_role'] .'"' : '';
			         
			         
			         // Selected Value
			         $has_selected_value = (!empty($default_selected_value)) ? ' alm-filter--preselected' : '';			
						
						
						// Build output
			         $output .= '<div class="alm-filter alm-filter--'. str_replace('_', '', $obj['key']) . $has_selected_value .'" id="alm-filter-'. $filterCount .'" data-key="'. $obj['key'] .'" data-fieldtype="'. $obj['field_type'] .'"'. $taxonomy_value . $taxonomy_operator .''. $meta_value . $meta_operator . $meta_type . $author_role .  $default_selected_value .''. $default_value .'>';

			         	$output .= self::alm_filters_display_title($obj);

			         	// Determine which $key to implement
			         	$key = $obj['key'];
                     $key = ($key === 'taxonomy') ? $obj['taxonomy'] : $key; // Convert $key to $taxonomy value
                     $key = ($key === 'meta') ? $obj['meta_key'] : $key; // Convert $key to $meta_key value


			         	// Check to see if custom filter exists
			         	$has_custom_values_filter = has_filter('alm_filters_'. $options_obj['id'] .'_'. self::alm_filters_revert_underscore($key));

			         	// Custom values or fitler and ! Textfield
			         	if(($obj['values'] || $has_custom_values_filter) && $obj['field_type'] !== 'text'){

				         	/*
   				          * Value filter hook
   				          */

				         	$values = apply_filters('alm_filters_'. $options_obj['id'] .'_'. self::alm_filters_revert_underscore($key), $obj['values']);

				         	// Pass Custom Values to function
				         	$output .= self::alm_filters_list_custom_values($values, $obj, $queryString);

			         	} else {

				         	if($obj['field_type'] === 'text'){

									$output .= self::alm_filters_display_textfield($obj, $queryString);

								} else {

                           /*
                            * Value filter hook
                           */


                           if(has_filter('alm_filters_'. $options_obj['id'] .'_'. $key)){
                              $values = apply_filters('alm_filters_'. $options_obj['id'] .'_'. self::alm_filters_revert_underscore($key), '');
                              $output .= self::alm_filters_list_custom_values($values, $obj, $queryString);

                           } else {

                              $output .= self::alm_filters_list_terms($obj, $queryString);

                           }

				         	}

			         	}

			         $output .= '</div>';

		      	}


		      	// Submit Button
		      	// Hide Submit button if count is 1 and field type is textfield.
		      	$hideSubmitBtn = ( $obj['count'] === '1' && !empty($obj['button_label']) && $obj['field_type'] === 'text' ) ? true : false;

		      	if($options_obj['style'] === 'button' && !$hideSubmitBtn){
			      	$output .= '<div class="alm-filter--submit">';
			      		$output .= '<button type="button" class="alm-filters--button">'. $options_obj['button_text'] .'</button>';
			      	$output .= '</div>';
		      	}


		      	/*
		          * alm_filters_edit
		          * Disable direct link edits of filter in admin
		          */
		         $is_filter_option = get_option(ALM_FILTERS_PREFIX . $options_obj['id']);
	         	if (is_user_logged_in() && current_user_can( 'edit_theme_options' ) && apply_filters( 'alm_filters_edit', true ) && !empty($is_filter_option)){
	         		$output .= '<a href="'. get_admin_url() .'admin.php?page=ajax-load-more-filters&filter='. $filters['id'] .'" class="alm-filters-edit">'. __('Edit Filter', 'ajax-load-more-filters') .'</a>';
	         	}

		      	$output .= '<div class="alm-filters--loading"></div>';

	         $output .= '</'.$container_element.'>';


	         // print the markup
	         return $output;
			}

   	}



   	// Return the key operators to core alm
   	// Deprecated in 1.1
   	public static function alm_filters_return_key_operators(){

	   	return self::$alm_filters_key_operators;

   	}



   	// Parse the querystring
   	public static function alm_filters_parse_url(){

	   	$url = $_SERVER['QUERY_STRING'];
	   	
	   	parse_str($url, $queryString);
	   	$queryString = (!empty($queryString)) ? str_replace(' ', '+', $queryString) : '';

	   	return $queryString;

   	}



   	// alm_filters_get_meta_keys()
		// Get all meta_key parameters from filter so we only parse the keys that matter in the URL
   	public static function alm_filters_get_meta_keys($target = ''){
		
			$array = [];
			
			if($target){ // Target is set in core ALM shortcode
				
				// Get filter option from DB
				$alm_filters = get_option(ALM_FILTERS_PREFIX . $target);
				if($alm_filters){
					$alm_filters = unserialize($alm_filters);
					$alm_filters = $alm_filters['filters'];
					
					// Loop all sub filters
					foreach($alm_filters as $alm_filter){
						if(isset($alm_filter['meta_key'])){
							// If meta_key, add to array
							$array[] = $alm_filter['meta_key'];
						}
					}			
				}		
			}
			
			return $array;
		}



   	// Convert key (shortcode params) to camelCase. e.g. post_type => postType
   	public static function alm_filters_replace_underscore($value){

	   	$underscore = strpos($value, '_');
	   	if($underscore){
		   	$charToReplace = substr($value, $underscore+1, 1);
		   	$value = str_replace('_'.$charToReplace, strToUpper($charToReplace), $value);
	   	}

	   	// If value is year, month or day add '_' before to prevent 404s. e.g. _year
	   	$value = ($value === 'year' || $value === 'month' || $value === 'day' || $value === 'author') ? '_'. $value : $value;

	   	return $value;
   	}



   	// Remove the leading _ from certain key values.
   	public static function alm_filters_revert_underscore($key){

	   	// If value is _year, _month, _day or _author remove the '_'
	   	$key = ($key === '_year' || $key === '_month' || $key === '_day' || $key === '_author') ? str_replace('_', '', $key) : $key;

	   	return $key;
   	}



   	// Render filter title.
   	public static function alm_filters_display_title($obj){

      	$output = '';
         if(!empty($obj['title'])){
            $output = '<div class="alm-filter--title"><'. apply_filters('alm_filters_title_element', 'h3') .'>'. $obj['title'] .'</'. apply_filters('alm_filters_title_element', 'h3') .'></div>';
         }
         return $output;

   	}



   	// Render custom values
   	public static function alm_filters_list_custom_values($custom_values, $obj, $queryString){

	   	if($obj['field_type'] === 'text') return false; // Exit if is textfield field_type

	   	$return = '';
	   	$items_count = 0;
	   	$selected_value = explode('+', $obj['selected_value']); // parse selected_value into array
	   	
	   	$return .= apply_filters('alm_filters_container_open', self::alm_filters_get_container($obj['field_type'], 'open'));

	   	foreach($custom_values as $v){

		   	$items_count++;
				$name = $v['label'];
				$slug = $v['value'];
				$obj['id'] = $obj['key'] .'-'. $obj['field_type'] .'-'. $obj['count'];
				$fieldname_val = $obj['key'] .'-'. $obj['field_type'] .'-'. $obj['count'];
				$fieldname = ($obj['field_type'] === 'radio') ? ' name="'. $fieldname_val .'"' : '';
				
				if($name === '' && $slug === ''){
					continue; // Exit this iteration if name and slug are empty.
				}
			
				// Querystring params
				$selected = $active = $matchArray = '';

				// Custom Fields
            if($obj['key'] === 'meta' && isset($queryString[$obj['meta_key']])){
            	$matchArray = explode('+', $queryString[$obj['meta_key']]);
            }
            
            // Taxonomy
            elseif($obj['key'] === 'taxonomy' && isset($queryString[$obj['taxonomy']])){
            	$matchArray = explode('+', $queryString[$obj['taxonomy']]);   				
            }
            
            // Everything else
            else {
            	if(isset($queryString[$obj['key']])){
            		$matchArray = explode('+', $queryString[$obj['key']]);
            	} else {
            		// Selected Value match
            		if(($obj['field_type'] === 'radio' || $obj['field_type'] === 'select')){
            			$matchArray = $selected_value;
            		}
            	}   				
            }
				

		   	switch ($obj['field_type']) {

					case 'select' :

						if(!empty($matchArray)){
							$selected = (in_array($slug, $matchArray)) ? ' selected="selected"' : '';
						}

						if($items_count === 1 && $obj['label']){
							$return .= '<option value="#"'. $selected .'>'. $obj['label'] .'</option>';
						}
						$return .= '<option id="'. $obj['field_type'] .'-'. $slug .'"'. $fieldname .' value="'. $slug .'"'. $selected .'>';
							$return .= $name;
						$return .= '</option>';

						break;

					default :					

						// Get active list item
						if(!empty($matchArray)){
							$active = (in_array($slug, $matchArray)) ? ' active' : '';
						}

						$return .= '<li class="alm-filter--'. $obj['field_type'] .'">';
							$return .= '<a href="javascript:void(0);" class="alm-filter--link field-'. $obj['field_type'] .' field-'. $slug . $active .'" id="'. $obj['field_type'] .'-'. $slug .'-'. $obj['count'] .'"'. ' data-type="'. $obj['field_type'] .'" data-value="'. $slug .'"'. $selected .'>';
								$return .= $name;
							$return .= '</a>';

						$return .= '</li>';

				}
	   	}

	   	$return .= apply_filters('alm_filters_container_close', self::alm_filters_get_container($obj['field_type'], 'close'));

	   	return $return;

   	}



   	// Render taxonomy terms (cat, tag, custom tax)
   	public static function alm_filters_list_terms($obj, $queryString){

	   	$return = '';
	   	$items = [];
	   	$items_count = 0;
         $exclude = explode(',', $obj['exclude']); // parse excludes into array
         $selected_value = explode('+', $obj['selected_value']); // parse selected_value into array
         $matchKey = $obj['key'];

	   	// Author
	   	if($obj['key'] === '_author' && isset($obj['author_role'])){
				$authors = get_users( array( 'role' => $obj['author_role'] ) );
				$terms = [];
				if($authors){
					$terms = [];
					foreach($authors as $author){
						//print_r($author);
						$terms[] = array(
							'term_id' => $author->ID,
							'slug' 	 => $author->ID,
							'name'	 => $author->display_name
						);
					}
				}
			}
	   	// Category
	   	if($obj['key'] === 'category' || $obj['key'] === 'category_and'){
				$terms = get_categories();
			}
			// Tag
	   	if($obj['key'] === 'tag' || $obj['key'] === 'tag_and'){
		   	$terms = get_tags();
			}
			// Taxonomy
	   	if($obj['key'] === 'taxonomy'){
		   	$matchKey = $obj['taxonomy']; // set $matchKey to taxonomy slug
		   	$terms = get_terms($obj['taxonomy']);
			}

			// Querystring params
			$selected = $active = $matchArray = '';

			if(isset($queryString[$matchKey])){
   			// Querystring match
				$matchArray = explode('+', $queryString[$matchKey]);
			} else {
   			// Selected Value match
   			if(($obj['field_type'] === 'radio' || $obj['field_type'] === 'select')){
   				$matchArray = $selected_value;
   			}
			}

			if(isset($terms) && $terms) {

   			// Loop each term and build an array of terms
				foreach ($terms as $term) {
					$term = (object)$term;
					// Build terms array, exclude where needed
	   			if(!in_array($term->term_id, $exclude)){
	   				$items[] = $term;
	   			}
	         }


				if($items){

					$return .= apply_filters('alm_filters_container_open', self::alm_filters_get_container($obj['field_type'], 'open'));

					foreach( $items as $item ) {

						$items_count++;
						$name = $item->name;
						$slug = $item->slug;
						
						// If category_and use ID
						$slug = ($obj['key'] === 'category_and') ? $slug = $item->term_id : $slug;
						
						// If tag_and use ID
						$slug = ($obj['key'] === 'tag_and') ? $slug = $item->term_id : $slug;
						
						
						$obj['id'] = $obj['key'] .'-'. $obj['field_type'] .'-'. $obj['count'];
						$fieldname_val = $obj['key'] .'-'. $obj['field_type'] .'-'. $obj['count'];
						$fieldname = ($obj['field_type'] === 'radio') ? ' name="'. $fieldname_val .'"' : '';
						
						switch ($obj['field_type']) {
						
							case 'select' :
							
								if(!empty($matchArray)){
									$selected = (in_array($slug, $matchArray)) ? ' selected="selected"' : '';
								}

								if($items_count === 1 && $obj['label']){
	   							$return .= '<option value="#"'. $selected .'>'. $obj['label'] .'</option>';
								}								
								
								$return .= '<option id="'. $obj['field_type'] .'-'. $slug .'"'. $fieldname .' value="'. $slug .'"'. $selected .'>';
									$return .= $name;
								$return .= '</option>';

								break;

							default :

								// Get active list item
								if(!empty($matchArray)){
									$active = (in_array($slug, $matchArray)) ? ' active' : '';
								}

								$return .= '<li class="alm-filter--'. $obj['field_type'] .'">';									
									
									$return .= '<a href="javascript:void(0);" class="alm-filter--link field-'. $obj['field_type'] .' field-'. $slug . $active .'" id="'. $obj['field_type'] .'-'. $slug .'-'. $obj['count'] .'"'. ' data-type="'. $obj['field_type'] .'" data-value="'. $slug .'"'. $selected .'>';
										$return .= $name;
									$return .= '</a>';

								$return .= '</li>';

						}

					}

					$return .= apply_filters('alm_filters_container_close', self::alm_filters_get_container($obj['field_type'], 'close'));

				}
			}

			return $return;
	   }



   	// Render textfield
   	public static function alm_filters_display_textfield($obj, $queryString){

	   	$text_id = $obj['key'] .'-'. $obj['field_type'];
	   	$output = '';

	   	// Querystring params
	   	$selected = '';
			if(isset($queryString[$obj['key']])){
				$selected = $queryString[$obj['key']];
			}else{
				// Selected Value (Not in Use)
   			//if(!empty($obj['selected_value'])){
   			   //$selected = $obj['selected_value'];
   			//}
			}

			$placeholder = (isset($obj['placeholder'])) ? 'placeholder="'. $obj['placeholder'] .'"' : '';
			$has_button = (!empty($obj['button_label'])) ? true : false;
			$field_class = ($has_button) ? ' has-button' : '';

	   	$output .= '<div class="alm-filter--'. $obj['field_type'] .'">';
				if($obj['label']){
					$output .= '<label for="'. $text_id .'">'. $obj['label'] .'</label>';
				}
				$output .= '<div class="alm-filter--'. $obj['field_type'] .'-wrap'. $field_class .'">';
				   $output .= '<input class="alm-filter--textfield textfield" id="'. $text_id .'" name="'. $text_id .'" type="text" value="'. $selected .'" '. $placeholder .' />';
				   $output .= ($has_button) ? '<button type="button">'. $obj['button_label'] .'</button>' : '';

				$output .= '</div>';
			$output .= '</div>';

			return $output;

	   }
	   


   	// Set the container element
   	public static function alm_filters_get_container($field_type, $location){

			if($field_type === 'checkbox' || $field_type === 'radio'){
				return '<'. (($location === 'close') ? '/' : '' ) .'ul>';
			}
			if($field_type === 'select'){
				$return = '';
				if($location === 'open'){
					$return .= '<div class="alm-filter--select '. apply_filters('alm_filters_select_class', '') .'">';
						$return .= '<select class="alm-filter--item">';
				}else{
						$return .= '</select>';
					$return .= '</div>';
				}
				return $return;
			}

   	}



   	/*
   	*  alm_enqueue_filters_admin_scripts
   	*  Enqueue filters admin js and css
   	*
   	*  @since 1.0
   	*/
   	public static function alm_enqueue_filters_admin_scripts(){

      	wp_enqueue_style( 'alm-filters-admin', ALM_FILTERS_URL. '/dist/css/admin_styles.css', '');
      	wp_enqueue_script( 'alm-filters-admin', ALM_FILTERS_URL. '/dist/js/admin.js', '', ALM_FILTERS_VERSION, true);

      	wp_localize_script(
      		'alm-filters-admin', 'alm_filters_localize', array(
      			'root' => esc_url_raw( rest_url() ),
      			'nonce' => wp_create_nonce( 'wp_rest' ),
      			'base_url' => get_admin_url() .'admin.php?page=ajax-load-more-filters',
      			'delete_filter' => __('Are you sure you want to delete', 'ajax-load-more-filters'),
      			'ordering_parameters' => __('Ordering Parameters', 'ajax-load-more-filters'),
      			'date_parameters' => __('Date Parameters', 'ajax-load-more-filters'),
      			'category_parameters' => __('Category Parameters', 'ajax-load-more-filters'),
      			'tag_parameters' => __('Tag Parameters', 'ajax-load-more-filters'),
      			'create_filter' => __('Create Filter', 'ajax-load-more-filters'),
      			'update_filter' => __('Save Changes', 'ajax-load-more-filters'),
      			'saved_filter' => __('Filter Saved', 'ajax-load-more-filters')
      		)
      	);

   	}



   	/*
   	*  alm_filters_installed
   	*  an empty function to determine if users is true.
   	*
   	*  @since 1.0
   	*/

   	function alm_filters_installed(){
   	   //Empty return
   	}



   	/*
   	*  alm_filters_shortcode_params
   	*  Build Filters shortcode params and send back to core ALM
   	*
   	*  Note: $target is converted to filters-target for data atts
   	*  @since 1.0
   	*/

   	function alm_filters_shortcode_params($filters, $target, $filters_analytics, $filters_debug, $options){
   		$return = ' data-filters="true"';
   		$return .= ' data-filters-target="'. $target .'"';
   		$return .= ' data-filters-analytics="'. $filters_analytics .'"';
   		$return .= ' data-filters-debug="'. $filters_debug .'"';

		   return $return;
   	}



   	/*
   	*  alm_filters_preloaded_args
   	*  Build the preload query $args for filters.
   	*
   	*  return $args;
   	*  @since 1.0
   	*/

   	function alm_filters_preloaded_args($args){
	   	$pg = self::alm_filters_get_page_num();
			$offset = ($pg > 1) ? ($pg * $args['posts_per_page']) - $args['posts_per_page'] : $args['offset'];

			$args['offset'] = $offset; // Set $args value

		   return $args;
   	}



   	/*
   	*  alm_filters_reveal_open
   	*  The .alm-reveal wrapper for each filter result block
   	*
   	*  @return $html
   	*  @since 1.0
   	*/

   	function alm_filters_reveal_open($container_classes, $canonicalURL, $preloaded = false){
      	
      	$preloaded_class = ($preloaded) ? ' alm-preloaded' : '';

      	$querystring = $_SERVER["QUERY_STRING"];
      	$querystring = ($querystring) ? '?'. $querystring : '';
      	$html = '<div class="alm-reveal alm-filters'. $preloaded_class . $container_classes .'" data-page="'. self::alm_filters_get_page_num() .'" data-url="'.$canonicalURL . $querystring .'">';

		   return $html;
   	}



   	/*
   	*  alm_filters_reveal_close
   	*  The closing /div of the .alm-reveal wrapper for each filter result block
   	*
   	*  @return $html
   	*  @since 1.0
   	*/

   	public static function alm_filters_reveal_close(){
      	$html = '</div>';
		   return $html;
   	}



   	/*
   	*  alm_filters_get_page_num
   	*  Return the current page number via querystring
   	*
   	*  @since 1.0
   	*/

   	public static function alm_filters_get_page_num(){
	   	$pg = (isset($_GET['pg'])) ? $_GET['pg'] : 1;
	   	return $pg;
   	}



   	/*
   	*  alm_filters_radio_select
   	*  Is the field a radio or select?
   	*
   	*  @return boolean
   	*  @since 1.2
   	*/

   	public static function alm_filters_radio_select($field){
	   	$return = false;
	   	if($field === 'radio' || $field === 'select'){
		   	$return = true;
	   	}
	   	return $return;
   	}
   	
   	

      /*
   	*  alm_get_all_filters
   	*  Get all filters from the wp_options table.
   	*
   	*  @return $filters array  an array of all filters
   	*  @since 1.1
   	*/

      public static function alm_get_all_filters(){
         global $wpdb;
      	$prefix = esc_sql( ALM_FILTERS_PREFIX );
      	$options = $wpdb->options;
      	$t  = esc_sql( "$prefix%" );
      	$sql = $wpdb -> prepare ( "SELECT option_name FROM $options WHERE option_name LIKE '%s'", $t );
      	$filters = $wpdb -> get_col( $sql );
      	
      	$filters = ALMFilters::alm_remove_filter_license_options($filters);

      	return $filters;
      }      
      
      
      
      /*
   	*  alm_remove_license_options
   	*  alm_filters_license_key & alm_filters_license_status are used as license keys - need to remove them from the list
   	*
   	*  @return $new_filters array an array of all active filters
   	*  @since 1.5
   	*/

      public static function alm_remove_filter_license_options($filters = ''){
         
         if($filters){            
            $new_filters = [];
      		foreach($filters as $filter){
      			if($filter !== 'alm_filters_license_status' && $filter !== 'alm_filters_license_key'){
      				$new_filters[] = $filter;
      			}
      		}
      		return $new_filters;
         }
      }     
      
      
      
      /*
   	*  alm_filters_replace_string
   	*  Replace alm_filter from option name
   	*
   	*  @return $string string
   	*  @since 1.5
   	*/

      public static function alm_filters_replace_string($string = ''){
         if($string){
         	$string = str_replace('alm_filter_', '', $string);
				return $string;
         }
      } 



   	/*
   	*  alm_filters_settings
   	*  Create the Comments settings panel.
   	*
   	*  @since 1.0
   	*/

   	function alm_filters_settings(){
      	register_setting(
      		'alm_filters_license',
      		'alm_filters_license_key',
      		'alm_filters_sanitize_license'
      	);
   	add_settings_section(
	   		'alm_filters_settings',
	   		'Filter Settings',
	   		'alm_filters_settings_callback',
	   		'ajax-load-more'
	   	);
	   	add_settings_field(  // Disbale CSS
				'_alm_filters_disable_css',
				__('Disable Filter CSS', 'ajax-load-more-filters' ),
				'alm_filters_disable_css_callback',
				'ajax-load-more',
				'alm_filters_settings'
			);
	   	add_settings_field(
	   		'_alm_filters_color',
	   		__('Color', 'ajax-load-more-filters' ),
	   		'alm_filters_color_callback',
	   		'ajax-load-more',
	   		'alm_filters_settings'
	   	);
   	}

   }


   /* Filter Settings (Displayed in ALM Core) */


	/*
	*  alm_filters_settings_callback
	*  Section Heading
	*
	*  @since 1.0
	*/

	function alm_filters_settings_callback() {
	   $html = '<p>' . __('Customize your installation of the <a href="http://connekthq.com/plugins/ajax-load-more/filters/">Filters</a> add-on.', 'ajax-load-more-filters') . '</p>';

	   echo $html;
	}


	/*
	*  alm_filters_disable_css_callback
	*  Diabale CSS.
	*
	*  @since 1.0
	*/

	function alm_filters_disable_css_callback(){
		$options = get_option( 'alm_settings' );
		if(!isset($options['_alm_filters_disable_css']))
		   $options['_alm_filters_disable_css'] = '0';

		$html = '<input type="hidden" name="alm_settings[_alm_filters_disable_css]" value="0" />';
		$html .= '<input type="checkbox" id="alm_filters_disable_css_input" name="alm_settings[_alm_filters_disable_css]" value="1"'. (($options['_alm_filters_disable_css']) ? ' checked="checked"' : '') .' />';
		$html .= '<label for="alm_filters_disable_css_input">'.__('I want to use my own CSS styles.', 'ajax-load-more-filters').'<br/><span style="display:block;"><i class="fa fa-file-text-o"></i> &nbsp;<a href="'.ALM_FILTERS_URL.'/dist/css/styles.css" target="blank">'.__('View Filter CSS', 'ajax-load-more-filters').'</a></span></label>';

		echo $html;
	}



	/*
	*  _alm_filters_color_callback
	*  Get the color of the paging element
	*
	*  @since 1.0
	*/

	function alm_filters_color_callback() {

	   $options = get_option( 'alm_settings' );

		if(!isset($options['_alm_filters_color']))
		   $options['_alm_filters_color'] = '0';

	   $color = $options['_alm_filters_color'];

		 $selected0 = '';
		 if($color == 'default') $selected0 = 'selected="selected"';

		 $selected1 = '';
		 if($color == 'blue') $selected1 = 'selected="selected"';

		 $selected2 = '';
		 if($color == 'red') $selected2 = 'selected="selected"';

		 $selected3 = '';
		 if($color == 'green') $selected3 = 'selected="selected"';


	    $html =  '<label for="alm_settings_filters_color">'.__('Choose the color of your filter elements', 'ajax-load-more-filters').'.</label><br/>';
	    $html .= '<select id="alm_settings_filters_color" name="alm_settings[_alm_filters_color]">';
	    $html .= '<option value="default" ' . $selected0 .'>Default</option>';
	    $html .= '<option value="blue" ' . $selected1 .'>Blue</option>';
	    $html .= '<option value="red" ' . $selected2 .'>Red</option>';
	    $html .= '<option value="green" ' . $selected3 .'>Green</option>';
	    $html .= '</select>';

	    $html .= '<div class="clear"></div>';

	    $html .= '<div class="ajax-load-more-wrap alm-filters filters-'.$color.'"><span class="pages">'.__('Preview', ALM_NAME) .'</span>';

    		// Checkbox
	 		$html .= '<div class="alm-filter" style="padding: 5px 0 20px; margin: 0; clear: both;">';
	 			$html .= '<li class="alm-filter--checkbox"><a href="javascript:void(0);" class="alm-filter--link field-checkbox active" data-type="checkbox" data-value="design">Checked</a></li>';
	 			$html .= '<li class="alm-filter--checkbox"><a href="javascript:void(0);" class="alm-filter--link field-checkbox" data-type="checkbox" data-value="design">Unchecked</a></li>';
	    	$html .= '</div>';

    		// Radio
	 		$html .= '<div class="alm-filter" style="padding: 10px 0 0; margin: 0; clear: both;">';
	 			$html .= '<li class="alm-filter--radio"><a href="javascript:void(0);" class="alm-filter--link field-radio active" data-type="radio" data-value="design">Checked</a></li>';
	 			$html .= '<li class="alm-filter--checkbox"><a href="javascript:void(0);" class="alm-filter--link field-radio" data-type="radio" data-value="design">Unchecked</a></li>';
	    	$html .= '</div>';

    		// Button
	 		$html .= '<div class="alm-filter" style="padding: 20px 0 5px; margin: 0; clear: both; min-width: 240px;">';
	 			$html .= '<div class="alm-filter--submit" style="margin: 0;"><button type="button" class="alm-filters--button" style="margin: 0;">'. apply_filters('alm_filters_button_text', __('Submit', 'ajax-load-more')) .'</button></div>';
	    	$html .= '</div>';

	   $html .= '</div>';


	    echo $html;
	?>

	<script>

    	// Filter Preview
    	var colorArrayFilters = "filters-default filters-red filters-green filters-blue";
    	jQuery("select#alm_settings_filters_color").change(function() {
    		var color = jQuery(this).val();
			jQuery('.ajax-load-more-wrap.alm-filters').removeClass(colorArrayFilters);
			jQuery('.ajax-load-more-wrap.alm-filters').addClass('filters-'+color);
		});
		jQuery("select#alm_settings_filters_color").click(function(e){
			e.preventDefault();
		});

		// Check if Disable CSS  === true
		if(jQuery('input#alm_filters_disable_css_input').is(":checked")){
	      jQuery('select#alm_settings_filters_color').parent().parent().hide(); // Hide button color
    	}

    	// On load
    	jQuery('input#alm_filters_disable_css_input').change(function() {
    		var el = jQuery(this);
	      if(el.is(":checked")) {
	      	el.parent().parent('tr').next('tr').hide(); // Hide color
	      }else{
	      	el.parent().parent('tr').next('tr').show(); // show color
	      }
	   });

    </script>

	<?php
	}



   /*
   *  alm_filters_sanitize_license
   *  Sanitize the license activation
   *
   *  @since 1.0.0
   */

   function alm_filters_sanitize_license( $new ) {
   	$old = get_option( 'alm_filters_license_key' );
   	if( $old && $old != $new ) {
   		delete_option( 'alm_filters_license_status' ); // new license has been entered, so must reactivate
   	}
   	return $new;
   }



   /*
   *  ALMFilters
   *  The main function responsible for returning Ajax Load More Filters.
   *
   *  @since 1.0
   */

   function ALMFilters(){
   	global $ALMFilters;
   	if( !isset($ALMFilters) ){
   		$ALMFilters = new ALMFilters();
   	}
   	return $ALMFilters;
   }
   ALMFilters(); // initialize


endif; // class_exists check




/*
* alm_filters
* The public function responsible for building the filters.
*
* @param $array array   Data to build filters
* @since 1.0
*/
function alm_filters($array, $target){
   return ALMFilters::init($array, $target);
}



/* Software Licensing */
function alm_filters_plugin_updater() {	
	if(!has_action('alm_pro_installed') && class_exists('EDD_SL_Plugin_Updater')){ // Don't check for updates if Pro is activated
		$license_key = trim( get_option( 'alm_filters_license_key' ) );
		$edd_updater = new EDD_SL_Plugin_Updater( ALM_STORE_URL, __FILE__, array(
				'version' 	=> ALM_FILTERS_VERSION,
				'license' 	=> $license_key,
				'item_id'   => ALM_FILTERS_ITEM_NAME,
				'author' 	=> 'Darren Cooney'
			)
		);
	}	
}
add_action( 'admin_init', 'alm_filters_plugin_updater', 0 );
/* End Software Licensing */
