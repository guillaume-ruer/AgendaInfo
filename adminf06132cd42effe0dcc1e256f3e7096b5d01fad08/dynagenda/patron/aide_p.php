<h1>Phrases d'aides</h1>

<table>
<?php foreach($phrase as $id => list($int, $cont) ) : ?>
	<tr>
		<td><?php echo $id+1 ?>)</td>
		<td><?php echo $int ?></td>
		<td><button data-id="<?php echo $id ?>" disabled >Sauvegarder</button></td>
		<td style="width:500px" ><textarea class="editor" data-id="<?php echo $id ?>" rows="10" cols="100" ><?php echo secuhtml($cont) ?></textarea></td>
	</tr>
<?php endforeach ?>
</table>

<script>

$(function(){
	$('.editor').ckeditor();
	
	for(var ind in CKEDITOR.instances )
	{
		CKEDITOR.instances[ind].on('blur', function(e){
			var id = $(this.element).attr('data-id'); 
			var txt = e.editor.getData(); 
			save_phrase(id, txt); 
			bt_save(id).prop('disabled', true); 
		});

		CKEDITOR.instances[ind].on('focus', function(e){
			var id = $(this.element).attr('data-id'); 
			bt_save(id).prop('disabled', false); 	
		});
	}

	function bt_save(id)
	{
		return $('[data-id='+id+']'); 
	}

	function save_phrase(id, txt)
	{
		$.post('aide.php', {
			'save_id' : id,
			'save_txt' : txt
		}); 
	}
});

</script>
