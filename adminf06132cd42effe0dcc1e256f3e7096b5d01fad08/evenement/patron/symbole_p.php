<h1>Liste des symboles</h1>

<p>
	<?php if( droit(GERER_SYMBOLE) ) : ?>
		<a href="symbole-form.php" >Nouveau</a> 
	<?php endif ?>
	<a href="grp-symbole.php" >Liste des groupes de symbole</a>
</p>

<?php pmess() ?>

<table class="table_defaut" >
	<tr>
		<th>Image</th>
		<th>Nom</th>
		<th>Groupe</th>
		<?php if( droit(GERER_SYMBOLE) ) : ?>
			<th>Editer</th>
			<th>Supprimer</th>
		<?php endif ?>
	</tr>
<?php while($do = $donne->parcours() ) : ?>
<tr>
	<td><?php $do->aff() ?></td>
	<td><?php echo $do->nom ?></td>
	<td><?php echo $do->groupe ?></td>
	<?php if( droit( GERER_SYMBOLE ) ) : ?>
		<td><a href="symbole-form.php?id=<?php echo $do->id ?>" >Editer</a></td>
		<td><a href="symbole.php?id=<?php echo $do->id ?>" 
			onclick="return confirm('Voulez vous vraiment supprimer ce symbole ? ') " >Supprimer</a>
		</td>
	<?php endif ?>
</tr>
<?php endwhile ?>
</table>
