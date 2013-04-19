
$(document).ready(function(){
	console.log('load custom.js')
	 

	$('.topbar_area img').on('click',function(){
		// console.log('go to playbasis_website')
		window.location = 'http://www.playbasis.com';
	})

		
		$('.detail_enclosure').show('fast');
		$('.apidoc_enclosure').hide('fast');

	$('#detail').on('click',function(){
		$('.detail_enclosure').hide('fast');  
		$('.apidoc_enclosure').show('fast');
	})


	$('#api').on('click',function(){
		$('.detail_enclosure').show('fast');
		$('.apidoc_enclosure').hide('fast');
	})


	// $('#detail').trigger('click')

	//help styling 
	$('.p_wrap').prepend('&nbsp;&nbsp;&nbsp;')
	$('h3.sub_header').attr('style','margin-left: 12px;font-size: 1.1em;font-weight: lighter;text-decoration: underline;color: #666;font-style: italic;')
	
}) 

