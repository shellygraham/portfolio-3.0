import { getDefault } from './Defaults';

/*
 * getTerms
 * Get selected terms of each filter object
 *
 * @param filter element   The container element for the current filter set
 * @param data object   The data obj for the filter
 * @return returnVal
 *
 * @since 1.0
 */

let getTerms = (filter, data) => {
	let count = 0;
	let returnVal = '';
	let value = '';
	let key = filter.dataset.key;
	let fieldtype = filter.dataset.fieldtype;
	let defaultValue = filter.dataset.defaultValue;
	
	switch (fieldtype){
		
		case 'select' : 
					
			let select = filter.querySelector('select');
			// Replace + with comma
			value = select.value.replace('+', ',');
			returnVal += (value === '#') ? '' : value;
			
			break;
		
		case 'text' : 
			
			let text = filter.querySelector('input');
			returnVal += (text.value === '') ? '' : text.value;
			
			break;
		
		default : 
			
			let items = filter.querySelectorAll('.alm-filter--link'); // Get all link fields	
			[...items].forEach((item, e) => {
   			
				if(item.classList.contains('active')){
					
					// Replace + with comma
					value = item.dataset.value.replace('+', ',');
					
					// If items have multiple selections split with comma
					if(count > 0){
						returnVal += ',';
					}
					returnVal += value;
					count++;
				}
				
			});
		
	}
	
	
	// If returnVal empty, check for a default/fallback
	if(!returnVal){
   	returnVal = getDefault(filter);
	}
	 
	
	return returnVal;
	
};

export default getTerms;