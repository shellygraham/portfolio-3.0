/*
 * almFiltersBuildURL
 * Send the final output to the almFilter() function in core ALM
 *
 * @param target string   The target ALM container 
 * @param data object   The data obj for the filter
 * @return null   ddispatch global almFilter() function call
 *
 * @since 1.0
 */

let buildURL = (filter, currentURL) => {
	
	let url = '';
	let key = filter.dataset.key;
	let fieldtype = filter.dataset.fieldtype;
	let taxonomy = filter.dataset.taxonomy;
	let metaKey = filter.dataset.metaKey;
	let metaOperator = filter.dataset.metaOperator; 
	let metaType = filter.dataset.metaType;	
	
	//let default_value = 'post';
	
	let title = (key === 'taxonomy') ? `${taxonomy}` : `${key}`; // Convert type to taxonomy slug
	title = (key === 'meta') ? `${metaKey}` : title; // Convert type to custom field slug
	
	
	// If current URL is empty, prepend ? for the querystring
	title = (currentURL === '') ? `?${title}` : `&${title}`;
	
	switch (fieldtype){
		
		case 'select' : 
			
			let select = filter.querySelector('select');
			url += (select.value === '#') ? '' : `${title}=${select.value}`;
			
			break;
		
		case 'text' : 
			let textfield = filter.querySelector('input[type=text]');
			url += (textfield.value === '') ? '' : `${title}=${textfield.value}`;
			
			break;
		
		default :  
			
			let items = filter.querySelectorAll('.alm-filter--link'); // Get all inputs
			let checkedVal = '';	
			let count = 0;
			
			[...items].forEach((item, e) => {
   			
				if(item.classList.contains('active')){					
					if(count > 0){
						checkedVal += '+';
					}
					checkedVal += item.dataset.value;
					count++;
				}
				
			});
			
			if(count > 0){
				url += `${title}=${checkedVal}`;
			}
		
	}
	
	// console.log(url);	
	return url;
	
};

export default buildURL;