$(function(){
	var chargement = '<img src="'+C_IMG+'chargement.gif" alt="chargement" />'; 

	$('.sup').click(function(){
		var lien = $(this); 
		$.get(lien.attr('href'), function(){
			lien.closest('tr').remove(); 
		}); 

		return false; 
	}); 

	function edite_click(ele)
	{
		var span = $(ele);
		var form = span.closest('.xform'); 
		var url = form.attr('data-action'); 
		var id = form.attr('data-id'); 
		var texte = span.html(); 
		var name = span.attr('data-name'); 
		var type = span.attr('data-type'); 

		if( type == "xselect" )
		{
			var select = $('<div style="position:absolute; background:white; z-index:1;" class="xselect" >');
			var data_option = span.attr('data-option'); 
			var option = XSELECT_OPTION[data_option]; 

			for(var i=0; i<option.length; i++)
			{
				var opt = $('<div style="padding:2px" >'+option[i].label+'</div>'); 

				opt.hover(function(){
					$('> div', select).css('background', 'inherit'); 
					$(this).css('background', '#FFFFAA'); 
				}); 

				opt.click( function(){
					var data_option = option[ $(this).index() ]; 
					var netat = data_option.value; 
					var ntexte = data_option.repl ? data_option.repl : data_option.label; 

					span.html(chargement); 

					$.post(url, {
						"id" : id,
						"name" : name,
						"value" : netat
					}, function(data){
						if( data == 0 )
						{
							span.empty();
							span.html(ntexte); 
						}
						else
						{
							message(span, "Une erreur s'est produite ("+data+")"); 
							span.html(texte); 
						}

						span.click(function(e){ edite_click(this); }); 

					}).fail(function(){
						message(span, 'Veuillez vérifier votre connexion internet.'); 
						span.html(texte); 
						span.click(function(e){ edite_click(this); }); 
					});
					
				}); 

				select.append(opt); 
			}

			var ann = $('<div style="text-align:center;font-size:110%;padding:3px;" >Annuler</div>'); 
			ann.hover(function(){
				$('> div', select).css('background', 'inherit'); 
				$(this).css('background', 'red'); 
			}); 

			ann.click(function(e){ 
				e.stopPropagation(); 
				span.empty();
				span.html(texte); 
				span.click(function(e){ edite_click(this); }); 
			}); 
			
			select.append(ann); 
			span.append(select); 
			span.unbind('click');

			var eo = span.offset();
			select.css('top', (eo.top-select.height()/2)+'px');
			select.css('left', (eo.left +span.width() )+'px'); 
		}
		else 
		{
			var champ; 

			var $block = $('<div>');
			var h = span.height(); 	
			var w = span.width(); 
			var so = span.offset(); 
			span.css({'width':w, 'height':h}); 

			$block.css({
				'position':'absolute',
				'left' : so.left,
				'top' : so.top,
				'background' : 'white',
				'width':w,
			});

			if( type == "wysiwyg" )
			{
				champ = $('<textarea>'); 
				champ.ckeditor(function(){ $(this).focus(); } ); 
				champ.val(texte); 
				var editor = champ.ckeditorGet(); 
				editor.on('blur', function(){ champ.blur(); }); 
				span.html(champ); 
			}
			else
			{
				champ = $('<textarea style="vertical-align:middle; width:100%;box-sizing: border-box;">'); 
				champ.keyup(function(){ ajuste(this) }); 
				champ.html(texte); 
				span.html(champ); 
				champ.focus(); 
				ajuste(champ); 
			}

			
			var $menu = $('<div>'); 
			var $valide = $('<button class="xform_valider" >Valider</button>').click(function(event){
				event.stopPropagation(); 	
				var ntexte = champ.val(); 
				span.html(chargement); 

				$.post(url, {
					"id" : id,
					"name" : name,
					"value" : ntexte
				}, function(data){
					if( data == 0 )
					{
						span.empty();
						span.html(ntexte); 
					}
					else
					{
						message(span, "Une erreur s'est produite. (n°"+parseInt(data)+")"); 
						span.html(texte); 
					}

					span.click(function(){ edite_click(this); }); 
					span.css({'width':'auto', 'height':'auto'}); 

				}).fail(function(data){
					message(span, 'Veuillez vérifier votre connexion internet.'); 
					span.html(texte); 
					span.click(function(){ edite_click(this); }); 
					span.css({'width':'auto', 'height':'auto'}); 
				});

			});

			var $annule = $('<button class="xform_annuler" >Annuler</button>').click(function(event){
				event.stopPropagation(); 	
				span.html(texte); 
				span.click(function(){ edite_click(this); });
				span.css({'width':'auto', 'height':'auto'}); 
			}); 

			$block.append(champ); 
			$menu.append($valide); 
			$menu.append($annule); 
			$block.append($menu); 
			span.html($block); 

			span.unbind('click'); 

		}
	}

	function message(ele, message)
	{
		var boite = $('<div class="message_flottant" >'); 
		boite.html(message); 
		boite.css('position', 'absolute'); 

		if( ele.width() < 300 )
		{
			boite.css('min-width', '300px' ); 
		}
		else
		{
			boite.css('width', ele.width() ); 
		}

		var eo = ele.offset();
		$('body').append(boite); 
		boite.css('left', eo.left + (ele.width()/2 - boite.width()/2)+'px'); 
		boite.css('top', (eo.top-boite.height() )+'px'); 


		boite.click(function(){
			$(this).remove(); 
		});
	}

	$('.edite').click(function(){
		edite_click(this); 
	}); 

	function ajuste(a)
	{
		$(a).height(0);
		var scrollval = $(a)[0].scrollHeight;
		if( scrollval < 40 )
		{
			scrollval = 40; 
		}
		$(a).height(scrollval);
		if (parseInt($(a).height()) > $(window).height() - 30) {
			$(document).scrollTop(parseInt($(a).height()));
		}
	}
}); 
