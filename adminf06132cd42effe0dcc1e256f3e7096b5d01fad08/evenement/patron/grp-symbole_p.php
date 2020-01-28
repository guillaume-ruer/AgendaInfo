<h1>Liste des groupes de symbole</h1>

<p>
	<?php if(droit(GERER_SYMBOLE) ) : ?>
	<a href="grp-symbole-form.php" >Nouveau</a> 
	<?php endif ?>
	<a href="symbole.php" >Liste des symboles</a>
</p>

<table class="table_defaut" >
	<tr>
		<th>Nom</th>
		<?php if(droit(GERER_SYMBOLE) ) : ?>
			<th>Editer</th>
			<th>Supprimer</th>
		<?php endif ?>
	</tr>

<?php while($do = $lsgroupe->parcours() ) : ?>
	<tr>
		<td><?php echo $do->nom ?></td>

		<?php if( droit(GERER_SYMBOLE ) ) : ?>
			<td><a href="grp-symbole-form.php?id=<?php echo $do->id ?>" >Editer</a></td>
			<td><a href="grp-symbole.php?id=<?php echo $do->id ?>" 
				onclick="return confirm('Voulez vous vraiment supprimer ce groupe de symbole?')" >Supprimer</a>
			</td>
		<?php endif ?>
	</tr>
<?php endwhile ?>

</table>



