$(function(){
	$('#menu > ul > li > span').mouseenter(function(){
		var $menu = $(this).next(); 

		if( $menu.attr('data-open') != 'true') 
		{
			menu_show($menu); 
		}
	});

	$('#menu > ul > li').mouseleave(function(){
		var $menu = $('ul', $(this) ); 

		if( $menu.attr('data-open') != 'true') 
		{
			menu_hide($menu); 
		}
	});

	$('#menu > ul > li > span').click(function(){
		var $menu = $(this).next(); 

		if( $menu.attr('data-open') == 'true') 
		{
			menu_hide($menu); 
			$menu.attr('data-open', 'false'); 
		}
		else
		{
			$('#menu [data-open="true"]').each(function(i, e){
				$(e).attr('data-open', 'false'); 
				menu_hide( $(e) ); 
			}); 

			menu_show($menu); 
			$menu.attr('data-open', 'true'); 

		}

		return false; 
	});

	function menu_show($menu)
	{
		// $menu.stop(); 
		// $menu.slideDown();
		$menu.show(); 
		$menu.prev().css('background-color', 'white'); 
	}

	function menu_hide($menu)
	{
		/*
		$menu.stop(); 
		$menu.slideUp(function(){
			$menu.prev().css('background-color', 'transparent'); 
		}); 
		*/

		$menu.prev().css('background-color', 'transparent'); 
		$menu.hide(); 
	}
});
