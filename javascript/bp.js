var bp_form = function(env){ 
	
	$('.bp_sup', env).click( function(){ 
		$('.bp_relatif input', $(this).closest('.bp_boite') ).attr('disabled', false); 
		$(this).parent().remove(); 
	});

	$('.bp_relatif input', env).keydown(function(e){
		var mess = $('.bp_proposition', $(this).parent() ); 
		code = e.keyCode || e.which ; 
		switch(code)
		{
			case 40 :
				// Bas 
				if( indice < $('> span', mess).length -1) 
					indice++; 
			break; 
			case 38 :
				// Haut 
				if( indice > 0 )
					indice--; 
			break; 
			case 13 :
				$('> span:nth('+indice+')', mess).triggerHandler('click'); 
			break; 
			default : 
				return;
		}

		$(' > span', mess).css({ backgroundColor:'inherit'}); 
		$(' > span:nth('+indice+')', mess).css({backgroundColor:'#BBBBFF'}); 
		mess.animate({scrollTop:25 * (indice - 4 ) }, 0); 

		e.preventDefault();
	}); 

	$('.bp_relatif input', env).each(function(e){ 
		var input=$(this); 
		var boite = input.parents('.bp_boite'); 
		var limite = boite.attr('data-limite'); 
		var etiquette = []; 

		$('.bp_multiple input[name="bp_id_'+input.attr('name')+'[]"]', boite).each(function(){ 
			etiquette.push( parseInt($(this).val() ) ); 
		}); 

		if( limite != '' && etiquette.length == limite )
		{
			input.attr('disabled', true); 	
			input.trigger('blur'); 
		}

	}); 

	$('.bp_relatif input', env).keyup(function(e){ 
		code = e.keyCode || e.which ; 

		if( e.keyCode == 13 || e.keyCode == 40 || e.keyCode == 38 )
		{
			return; 
		}
		
		index=0; 
		var input=$(this); 
		var bp = $('.bp_proposition', $(this).parent() ); 
		var val= input.val(); 
		var boite = input.parents('.bp_boite'); 
		var fichier = boite.attr('data-bp-fichier'); 
		var limite = boite.attr('data-limite'); 
		bp.empty(); 

		if( val.length > 1 )
		{
			var donne = { nom : val }; 
			var sel = []; 

			$('.bp_multiple input[name="bp_id_'+input.attr('name')+'[]"]', boite).each(function(){ 
				sel.push( parseInt($(this).val() ) ); 
			}); 

			$.ajax({ 
				url : ROOT_PATH+'ajax/'+fichier, 
				success : function ( data ){ 
					bp.empty(); 
					$.each( data, function(cle, val) { 
						if( $.inArray(val.id, sel)!=-1 )
						{
							return; 
						}

						var ele = $(
							'<span class="bp_mot_cle" id="num_'+val.id+'" '
							+ ' data-etiquette="'+htmlentities(val.etiquette)+'" '
							+ ' data-donnee="'+htmlentities(val.json)+'" '
							+ '/>'
						);
						ele.html(val.proposition);

						ele.click( function () { 
							var idc = $(this).attr('id').substring(4); 
							var etiquette = $(this).attr('data-etiquette'); 
							var donne = $(this).attr('data-donnee'); 

							var sp = $('<span class="bp_choi" >'+etiquette+'</span>'); 
							sp.append( $('<a>', {
								click : function(){ 
									$(this).parent().remove(); 
									input.attr('disabled', false); 
								},
								text : 'X'
							}));

							sp.append( $('<input type="hidden" name="bp_id_'+input.attr('name')+'[]" value="'+idc+'" />') ); 
							sp.append( $('<input type="hidden" name="bp_do_'+input.attr('name')+'[]" />').val(donne) ); 

							input.focus(); 	
							$('.bp_multiple', boite).append(sp); 
							$(this).remove(); 

							var etiquette = []; 

							$('.bp_multiple input[name="bp_id_'+input.attr('name')+'[]"]', boite).each(function(){ 
								etiquette.push( parseInt($(this).val() ) ); 
							}); 

							if( limite != '' && etiquette.length == limite )
							{
								input.attr('disabled', true); 	
								input.val(''); 
								bp.empty(); 
								input.trigger('blur'); 
							}
						}); 

						ele.hover(function(){
							$(' > span', bp).css({ backgroundColor:'inherit'}); 
							$(this).css({ backgroundColor:'#BBBBFF'}); 
							indice = $(this).index(); 
						});

						bp.append(ele); 
					}); 

					indice=0; 
					$(' > span', bp).css({ backgroundColor:'inherit'}); 
					$(' > span:nth(0)', bp).css({backgroundColor:'#BBBBFF'}); 
				}, 
				data : donne,
				dataType : "json", 
				type : "GET"
			}); 
		}
	});
	
}; 

$(function(){ 
	var indice=0;

	bp_form(this); 
}); 
