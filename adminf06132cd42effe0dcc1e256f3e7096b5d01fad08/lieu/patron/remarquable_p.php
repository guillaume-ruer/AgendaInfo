<h1>Remarquable</h1>

<p><a href="rem-form.php" >Nouveau</a></p>

<?php $lsr->acc_pagin()->affiche() ?>

<table>
<?php while($r = $lsr->parcours() ) : ?>
	<tr>
		<td><?php $r->titre ?></td>
		<td><?php $r->ville()->nom ?></td>
		<td><?php $r->type ?></td>
		<td><a href="rem-form.php?id=<?php $r->id ?>" >Modifier</a></td>
		<td><a class="sup-rem" href="sup-rem.php?sup=<?php $r->id ?>" >Supprimer</a></td>
	</tr>
<?php endwhile ?>
</table>

<script>
$(function(){
	$('.sup-rem').click(function(){
		var bt = $(this); 

		if( confirm('Voulez vous vraiment supprimer cet élément ? ') )
		{
			$.get($(this).attr('href'), function(data){
				if( data.state == 'success')
				{
					bt.closest('tr').remove(); 
				}
			});
		}

		return false; 
	});
});

</script>
