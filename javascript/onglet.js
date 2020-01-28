var onglet_form = function(env){ 
	$(".og_titre", env).click(function() {
		var p = $(this).parents(".og_groupe")[0]; 

		if( $("> .og_page:eq("+ $(this).index()+")", p).is(':not(:visible)') )
		{
			$(".og_titre").parent().find('.og_titre').removeClass('active_titre');
			$(this).addClass('active_titre');
		
			$("> .og_page", p).slideUp(); 
			$("> .og_page:eq("+ $(this).index()+")", p).slideDown(); 
			$('#'+$('input', $(this) ).attr('id') ).prop('checked', true); 
		}
	}); 
}; 

$(function(){
	$(".og_page").each(function(i){
		if( $('.og_titre input[type=radio]', this).is(':not(:checked)') )
		{
			$(this).hide(); 
		}
		else
		{
			$('.og_titre', this).addClass('active_titre');
		}
	});	

	$(".og_groupe").each(function(i){
		$(this).prepend( $('<div>') ); 
	});

	$(".og_groupe .og_page .og_titre").each( function(i){ 
		var p = $(this).parents(".og_groupe")[0]; 
		$(this).appendTo( $(':first', p) );
	}); 

	$(".og_titre").css("float", "left"); 
	$(".og_groupe .og_page").css('clear', 'both'); 
	onglet_form(this); 
});
