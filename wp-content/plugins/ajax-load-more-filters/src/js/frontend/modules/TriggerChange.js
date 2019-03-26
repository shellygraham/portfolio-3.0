import vars from '../global/Variables'; 
import buildDataObj from './BuildDataObj';
import getTerms from './GetTerms';
import buildURL from './BuildURL';
import dispatch from './Dispatch';


/*
 * triggerChange
 * Get the selected terms and append them to data obj
 *
 * @param almFilters element   The filters elements
 * @return null 
 *
 * @since 1.0
 */

let triggerChange = (almFilters) => {   
   
   vars.alm_filtering = true;
   almFilters.classList.add('filtering');
	
	let target = almFilters.dataset.target; // Get target		
	let data = {}; // Define data object
	let url = ''; // Build URL
	let count = 0;	
	
	// Get the target .ajax-load-more element
	let alm = document.querySelectorAll('.ajax-load-more-wrap[data-id="'+ target +'"]');
	alm = alm[0];
	
	// Get canonicalUrl for empty pushState
	let canonicalUrl = alm.dataset.canonicalUrl;
	
	// Loop all filters
	let filters = almFilters.querySelectorAll('.alm-filter');
	[...filters].forEach((filter, e) => {
		count++; 
		data = buildDataObj(filter, data); // Build data obj
		url += buildURL(filter, url); // Build the URL
	});
	
	url = (url === '') ? canonicalUrl : url;
	
	let state = {
		permalink: url
	};
	
	// If pushstate is enabled and not triggered via popstate
	if(!vars.alm_filtering_popstate){
		
      //history.replaceState(state, null, url);
      history.pushState(state, null, url);
      
      /*
	    * almFiltersURLUpdate
	    * Callback function dispatched after the browser URL has been updated
	    *
	    */
      if (typeof window.almFiltersURLUpdate === "function") { 
			window.almFiltersURLUpdate(url);
		}
   }
   vars.alm_filtering_popstate = false;
   
	data.pause = false; // Disable pause (prevention)
	data.preloaded = false; // Disable preloaded (prevention)
	data.target = target; // Set target before data obj is sent	
	
	dispatch(target, data, url); // Dispatch the almFilters() function
	
};

export default triggerChange;