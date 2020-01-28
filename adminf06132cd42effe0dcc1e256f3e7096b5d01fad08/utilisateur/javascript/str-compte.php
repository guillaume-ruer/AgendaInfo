<?php 
include '../../../include/init.php'; 
?>
$(function(){
	$('.str_infos').hide(); 

	$('.active').click(function(){
		var $this = $(this); 
		var $bl = $this.closest('tr'); 
		var id = $bl.attr('data-id');

		$.get('ajax/active-str.php',{
			'id' : id,
			'etat' : $this.val()
		}, function(data){
			$bl.removeClass('inactif attente actif').addClass(data); 
			$this.prop('checked', true); 
		}); 

		return false; 
	});

	$('#str-table').on('click', '.ouvre-facture-form', function(){
		var $bl = $(this).closest('tr'); 
		reset_facture_form( $bl.find('form') );
		$('.ajt-facture-form', $bl).toggle(); 
		return false; 
	});

	$('#str-table').on('click', '.ajt-facture', function(){
		var $bl_infos = $(this).closest('tr'); 
		var $bl_str = $bl_infos.prev(); 
		var id = $bl_str.attr('data-id'); 
		var form_data = new FormData(); 
		var idf = $('input[name=idf]', $bl_infos).val(); 
		var ch_fichier = $('.ch-fichier', $bl_infos)[0]; 
		
		if( ch_fichier.files && ch_fichier.files[0])
		{
			form_data.append('file', ch_fichier.files[0]);
		}

		form_data.append('id', id); 
		form_data.append('idf', idf); 
		form_data.append('somme', $('input[name=somme]', $bl_infos).val() );
		form_data.append('type', $('select[name=type]', $bl_infos).val() ); 
		form_data.append('date', $('[name=date]', $bl_infos).val() ); 

		$.ajax({
			url : 'ajax/ajt-facture.php', 
			data : form_data,
			type : 'POST', 
			processData: false,
			contentType: false,
			success : function(ligne){
				reset_facture_form( $('form', $bl_infos) ); 

				if( idf )
				{
					$('.ls-facture tr[data-id='+idf+']').replaceWith(ligne); 
				}
				else
				{
					$('.ls-facture tbody', $bl_infos).append(ligne); 
				}
			}
		}); 

		return false; 
	}); 

	function reset_facture_form($form)
	{
		$form[0].reset(); 
		$('[name=idf]', $form).val(''); 
		var d = new Date(); 
		var strDate = d.getDate()+ "/"+(d.getMonth()+1) + "/"+d.getFullYear();
		change_date($form, strDate); 
	}

	$('#str-table').on('click', '.facture-annule', function(){
		var $bl = $(this).closest('.bl-infos'); 
		$bl.find('.ls-facture tr').css('background', 'transparent'); 
		reset_facture_form( $bl.find('form') ); 
		return false; 
	});

	$('#str-table').on('click', '.facture-modif', function(){
		var $ligne = $(this).closest('tr'); 
		var $bl = $(this).closest('.bl-infos'); 
		var data = $.parseJSON( $(this).closest('td').find('.facture-data-raw').text() );
		var $form = $('.ajt-facture-form', $bl);

		$(this).closest('table').find('tr').css('background', 'transparent'); 
		$ligne.css('background', 'yellow'); 
		$form.show(); 
		$form.find('[name="somme"]').val( data.somme); 
		$form.find('[name="type"] option[value='+data.type+']').prop('selected', true); 
		$form.find('[name="idf"]').val(data.id); 
		change_date($form, data.date_text); 
		return false; 
	});

	function change_date($form, date)
	{
		$form.find('[name="date"]').val(date); 
		$form.find('.aff-date').text(date); 
	}

	$('.str-facture').click(function(){
		var $bl = $(this).closest('tr'); 
		var $bl_info = $bl.next(); 
		$bl_info.toggle(); 

		if( !$bl_info.attr('data-ouvert') )
		{
			$('td', $bl_info).load( 'ajax/infos.php?id='+$bl.attr('data-id'), function(){
				$bl_info.attr('data-ouvert', 'vrai'); 
				reset_facture_form( $bl_info.find('form') ); 
			}); 
		}

		return false; 
	});

	(function(){
		var $bl_infos; 

		var JC = Calendar.setup({
			animation:false, 
			selectionType:Calendar.SEL_SINGLE, 
			onSelect : function(){
				var date = this.selection.get(); 
				date = Calendar.intToDate(date); 
				date = Calendar.printDate(date, '%e/%o/%Y');
				change_date($bl_infos.find('form'), date); 
				this.hide(); 
			}
		});

		$('#str-table').on('click', '.date-facture-bt', function(){
			var $this = $(this); 
			$bl_infos = $(this).closest('.bl-infos'); 
			JC.popup( $this[0], 'B'); 
			return false; 
		});


	})();

	(function(){
		var id; 

		var JC = Calendar.setup({
			animation:false, 
			selectionType:Calendar.SEL_SINGLE, 
			onSelect : function(){
				if( confirm('Etes vous sûre de vouloir changer la date de fin d\'adhésion ?') )
				{
					var date = this.selection.get(); 
					date = Calendar.intToDate(date); 
					date = Calendar.printDate(date, '%e/%o/%Y');

					$.get('ajax/date-adh.php',{
							'date' : date,
							'id' : id
						},function(){
							$('tr[data-id='+id+'] .date-adh').text(date); 
						}).fail(function(jqXHR,textStatus,errorThrown){
							alert(textStatus); 	
							alert(jqXHR);
							alert(errorThrown);
					});
				}

				this.hide();
			}
		});

		$('.maj-date-bt').click(function(){
			var $this = $(this); 
			id = $(this).closest('tr').attr('data-id');
			JC.popup( $this[0], 'B'); 
			return false; 
		});
	})();
});
