<h1>Gestion des préfixe</h1>

<p><a href="prefix-form.php" >Ajout</a></p>

<?php pmess() ?>

<table class="table_defaut" >
	<tr>
		<th></th>
		<th>Texte</th>
		<th></th>
	</tr>
<?php while($do = $prefixe->parcours() ) : ?>
	<tr>
		<td><a href="prefix-form.php?idp=<?php echo $do->id ?>" >Edit</a></td>
		<td><?php echo $do->prefixe ?></td>
		<td>[<a href="prefix.php?sup=<?php echo $do->id ?>" >X</a>]</td>
	</tr>
<?php endwhile ?>
</table>


