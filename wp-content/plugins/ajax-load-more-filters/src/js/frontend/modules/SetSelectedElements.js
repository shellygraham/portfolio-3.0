import vars from '../global/Variables'; 
import triggerChange from './TriggerChange';
import setCheckboxState from './SetCheckboxState';
import clearInputs from './ClearInputs';


/*
 * setElementStates
 * Get the selected terms and build the data obj
 *
 * @param filter element   The container element for the current filter set
 * @param data object   The data obj for the filter
 * @return data
 *
 * @since 1.0
 */

let setElementStates = (urlArray) => {
	
	vars.alm_filtering_popstate = true;
	let filters = vars.almFilters.querySelectorAll('.alm-filter');
	
	// Loop all filters
   [...filters].forEach((filter, e) => {
      let fieldtype = filter.dataset.fieldtype;
      let key = filter.dataset.key;
      key = (key === 'taxonomy') ? filter.dataset.taxonomy : key; // If key is taxonomy, convert key to taxonomy slug 
      key = (key === 'meta') ? filter.dataset.metaKey : key; // If key is meta, convert key to meta key           
      
      switch (fieldtype){	      
         
         case 'checkbox' :  // Checkbox
         
            let checkboxes = filter.querySelectorAll('a.field-checkbox'); // All checkboxes     
                   
            // If key matches URL key
            if (urlArray.hasOwnProperty(key)){
               let valueArray = urlArray[key].split('+');
               //console.log(valueArray);
               [...checkboxes].forEach((checkbox) => {
                  setCheckboxState(valueArray, checkbox);
               });
               
            } else {
               // Clear all checkboxes
               clearInputs(checkboxes);
            }   
         
         break;    
         
         case 'radio' : // Radios
                      
            let radios = filter.querySelectorAll('a.field-radio'); // All radios
                  
            if (urlArray.hasOwnProperty(key)){
               let radio = filter.querySelector('a[data-value="'+ urlArray[key] +'"]');
               let valueArray = urlArray[key].split('+');
               [...radios].forEach((radio) => {
                  setCheckboxState(valueArray, radio);
               });
               
            } else {
               // Clear all radios
               clearInputs(radios);
            }
          
         break;
         
         case 'select' : // Select 
         
            let select = filter.querySelector('select');
            if (urlArray.hasOwnProperty(key)){
               select.value = urlArray[key];               
            } else {
               select.value = '#';
            }
          
         break;
         
         default : // Textfield
         
            let textfield = filter.querySelector('input[type=text]');
            if (urlArray.hasOwnProperty(key)){
               textfield.value = urlArray[key];               
            } else {
               textfield.value = '';
            }
         
         break;

      }  
                
      
   });
   
   triggerChange(vars.almFilters);
	
};
export default setElementStates;
