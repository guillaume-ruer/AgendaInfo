<?php require '../../include/init.php' ?>

$(function(){
    $('#ch_lieu').autocomplete({
        source : '<?php echo ADD_SITE ?>membre/ajax/lieu.php',
        minLength : 2, 
        select : function(event, ui){ 
			$('#ville_id').val(ui.item['id']); 
        },
		response : function(event, ui ){
			$('#ville_id').val(''); 
		}
    });  

});
