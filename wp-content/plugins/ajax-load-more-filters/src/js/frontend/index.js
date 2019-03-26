import vars from './global/Variables'; 
import triggerChange from './modules/TriggerChange'; 
import setElementStates from './modules/SetSelectedElements'; 
import parseQuerystring from './modules/ParseQuerystring'; 
import buildDataObj from './modules/BuildDataObj';
import dispatch from './modules/Dispatch';
import { setDefaults, restoreDefault } from './modules/Defaults'; 
import setCurrentFilters from './modules/CurrentFilters';
import getKeyObject from './modules/currentFilters/getKeyObject';
import getKeyElement from './modules/currentFilters/getKeyElement'; 
require('./helpers/array.from'); 


/* 
 * almFiltersInit
 * Initiate the filter object
 *
 * @param almFilters element   The container element for the almFilters
 * @since 1.0
 */ 
let almFiltersInit = (almFilters) => {  
   
	let style = almFilters.dataset.style; // change, button   
   
   // Click/Change Event
   let almFiltersClick = (e) => {  
      if(vars.alm_filtering) return true; // exit if animating/loading            
	
	
		/*
	    * almFiltersChange
	    * Callback function is dispatched when a filter change event is triggered.
	    *
	    */
		if (typeof window.almFiltersChange === "function") { 
			window.almFiltersChange();
		}
		        
      triggerChange(almFilters);       
   }; 
   
   
   // Radio + Checkbox Click Event
   let almFilterChange = (event) => {
	   
	   let fieldtype = event.target.dataset.type;	   
		let current_id = event.target.id;
		let parent = event.target.parentNode.parentNode; // <ul/>
		let parentDiv = event.target.parentNode.parentNode.parentNode; // <div .alm-filter />
		
		
		if(fieldtype === 'radio'){
			
			// All radios
		   let radios = parent.querySelectorAll('.alm-filter--link');
		   
		   // Exit is active and preselected value set
		   if(parentDiv.classList.contains('alm-filter--preselected') && event.target.classList.contains('active')){
			   event.preventDefault();
			   return false;
		   }
		   
		   // Loop all radios
		   [...radios].forEach((radio, e) => {
			   if(radio.id !== current_id){
				   radio.classList.remove('active');
			   } 
		   });		   
	   }	   
	   
	   if(event.target.classList.contains('active')){
		   event.target.classList.remove('active');
	   } else {
		   event.target.classList.add('active');
	   }   
	   
	   if(style === 'change'){
		   triggerChange(almFilters);
		}
		
   }
   
   
   // Radio + Checkbox Event listeners
   let almFilterLinks = document.querySelectorAll('.alm-filter--link');
   if(almFilterLinks){
	   [...almFilterLinks].forEach((item, e) => {
	      item.addEventListener('click', almFilterChange);
	   });
   }  
   
   
   // Textfield Button Event listeners
   let almFiltertextButtons = document.querySelectorAll('.alm-filter--text-wrap.has-button button');
   if(almFiltertextButtons){
	   [...almFiltertextButtons].forEach((button, e) => {
	      button.addEventListener('click', almFiltersClick);
	   });
   }   
      
   
   // Change Event (Select)
   if(style === 'change'){
	   // Loop all items and add the event listener
	   let almFilterItems = document.querySelectorAll('.alm-filter--item');
	   if(almFilterItems){
		   [...almFilterItems].forEach((item, e) => {
		      item.addEventListener('change', almFiltersClick);
		   });
	   }
   }
   
   
   // Button
   if(style === 'button'){
	   let almFilterButton = almFilters.querySelector('.alm-filters--button');
	   if(almFilterButton){
		   almFilterButton.addEventListener('click', almFiltersClick);
		}
   }
   
   
   // Attach enter click listener for textfields
   let almFilterTextfields = document.querySelectorAll('.alm-filter--textfield');
   if(almFilterTextfields){
	   [...almFilterTextfields].forEach((item, e) => {
	      item.addEventListener('keyup', function(e){
				if (e.keyCode === 13) { // Enter/return click
					almFiltersClick();
				} 

	      });
	   });
   }
   
   // Set currently selected filters
	setCurrentFilters(window.location.search);	
   
};



/* 
 * removeSelectedFilter
 * Trigger click event on selected filter
 *
 * @param element   The clicked element
 * @since 1.0
 */ 
window.removeSelectedFilter = (element) =>{
	
	let almFilters = vars.almFilters;
	let key = element.dataset.key;
	let value = element.dataset.value;	
	let obj = getKeyObject(key, value); // Return the el container (.alm-filter)
	let el = getKeyElement(obj.target, value, obj.fieldType);
	
	switch (obj.fieldType){
		
		case 'select' :
			// if has a selected value
			el.value = (obj.target.dataset.selectedValue) ? obj.target.dataset.selectedValue : '#';
			triggerChange(almFilters);
			break;
		
		case 'text' : 
			el.value = '';
			triggerChange(almFilters);
			break;
		
		default : 
			el.click();
			if(almFilters.dataset.style === 'button'){
				triggerChange(almFilters)
			}
		 
	}
	
}




/* 
 * almFiltersPaged
 * Created paged URL parameters
 * Triggered from core ALM on load more click [core/src/js/ajax-load-more]
 *
 * @param alm element   Core ALM object
 * @since 1.0
 */ 
let almFiltersPage = '';
window.almFiltersPaged = (alm) => {
	
	// Exit if finished
	if(alm.finished) return false;
   
   let page = alm.page + 1;
   page = (alm.preloaded === 'true') ? page + 1 : page;	   
   
   let querystring = window.location.search.substring(1);
	let obj = {};
	
	if(querystring){		
	   obj = parseQuerystring(querystring);
	   if(Object.keys(obj).length){		   
		   obj.pg = page;			   
	   } 
   } else {	   
	   obj.pg = page;   
   }
   
   let url = '?';
   
   let count = 0;   
   Object.keys(obj).forEach(function(key) {
      count++;      
      if(count > 1){
         url += '&';
      }
      url += `${key}=${obj[key]}`; 
   }); 
   
   let state = { permalink: url };
	
	// Set the URL - don't allow for bck/fwd interactions while paging
   //history.replaceState(state, null, url);
	
}


// Init ALM Filters
if(vars.almFilters){
   almFiltersInit(vars.almFilters);
} 



/*
 * popstate 
 * Fires when users click back or forward browser buttons
*/

window.addEventListener('popstate', function(event) { 
     
   if(vars.almFilters){ // If element exists
      
      let url = (event.state) ? event.state.permalink : window.location.search; // Get state or querystring            
      url = url.replace('?', ''); // remove `?` param
      
      // Empty URL      
      if(url === '' || url === null){
         
         almFiltersClear(false);
	      
      } else {   
            
      	let urlArray = parseQuerystring(url); // [helpers/helpers.js]   
      	setElementStates(urlArray); // [modules/setSelectedElements.js]     
      	   	 
      }
         
   }  
});


/* 
 * almFiltersClear
 * Set all filter back to default state
 * Public JS function
 *
 * @param reset boolean
 * @updated 1.5.1
 * @since 1.0
 */ 
window.almFiltersClear = function(reset = true){
	
	//console.log('clearFilters');
	let target = vars.almFilters.dataset.target; // Get target
	let filters = vars.almFilters.querySelectorAll('.alm-filter'); // Get all filters
	let data = {}; // Define data object
	
	// Loop all filters
	[...filters].forEach((filter, e) => {
	   restoreDefault(filter);
		data = buildDataObj(filter, data);
	});
	
	if(reset){
		
		triggerChange(vars.almFilters);
		
	} else {
		
		// Dispatch filter change
		dispatch(target, data);
		
	}
	
}


/* 
 * almFiltersAddonComplete
 * Filters Complete function
 * Fires from core Ajax Load More [core/src/js/modules/filtering.js]
 *
 * @param el element   The alm element
 * @since 1.0
 */ 
 
window.almFiltersAddonComplete = function(el = null){
   setTimeout(function(){ // Delay re-initialization
      vars.alm_filtering = false;
      vars.almFilters.classList.remove('filtering');       
		
		/*
	    * almFiltersComplete
	    * Callback function dispatched after the filters have completed their magic
	    *
	    */
		if (typeof window.almFiltersComplete === "function") { 
			window.almFiltersComplete();
		}
		
   }, 250);
};