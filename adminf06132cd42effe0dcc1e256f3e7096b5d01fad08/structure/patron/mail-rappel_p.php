<h1>Mail de rappel</h1>

<p>{USER} sera remplacé par l'identifiant de connexion de l'utilisateur qui recevra le mail.</p>

<table>
<?php foreach($phrase as $id => list($int, $cont, $sujet) ) : ?>
	<tr data-id="<?php echo $id ?>" >
		<td><?php echo $id+1 ?>) <?php echo $int ?><br />
		<button class="enr"  disabled >Sauvegarder</button></td>
		<td style="width:100%" >
			<input style="width:100%" type="text" name="sujet" value="<?php echo $sujet ?>" /><br />
			<textarea class="editor" data-id="<?php echo $id ?>" rows="10" cols="100" ><?php echo secuhtml($cont) ?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="50" >
			<hr />
		</td>
	</tr>
<?php endforeach ?>
</table>

<script>

$(function(){
	$('.editor').ckeditor();

	$('.enr').click(function(){
		var bl = $(this).closest('tr'); 
		var id = bl.attr('data-id'); 
		var txt = bl.find('textarea').ckeditorGet().getData(); 
		var sujet = $('input').val(); 
		bl.find('.enr').prop('disabled', true); 

		save_phrase(id, txt, sujet); 
	});

	$('[name="sujet"]').focus(function(){
		var bl = $(this).closest('tr'); 
		bl.find('.enr').prop('disabled', false); 
	}); 

	$('[name="sujet"]').blur(function(){
		var bl = $(this).closest('tr'); 
		bl.find('.enr').prop('disabled', true); 
	}); 
	
	for(var ind in CKEDITOR.instances )
	{
		CKEDITOR.instances[ind].on('blur', function(e){
			var id = $(this.element).attr('data-id'); 
			$('tr[data-id='+id+'] .enr').click(); 
		});

		CKEDITOR.instances[ind].on('focus', function(e){
			var id = $(this.element).attr('data-id'); 
			$('tr[data-id='+id+'] .enr').prop('disabled', false); 	
		});
	}

	function save_phrase(id, txt, sujet)
	{
		$.post('mail-rappel.php', {
			'save_id' : id,
			'save_txt' : txt, 
			'save_sujet' : sujet
		}); 
	}
});

</script>
