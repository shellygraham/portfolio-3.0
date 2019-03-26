jQuery(document).ready(function($){

	// Work filtering anumation
	$( ".alm-filter--title" ).click(function() {
		$( "#clear-filters" ).toggle( "fade" );
		$( ".alm-filters .alm-filter--title h3" ).toggleClass( "arrow-up" );
		$( ".alm-filter ul" ).toggle( "drop, down" );
	});

	// Work filtering Clear/reset button
	var clearBtn = document.getElementById('clear-filters');
	clearBtn.addEventListener('click', function(e){
	   window.almFiltersClear();
	});
		
});