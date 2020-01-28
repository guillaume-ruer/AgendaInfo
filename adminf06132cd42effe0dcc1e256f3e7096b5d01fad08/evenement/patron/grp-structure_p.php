<h1>Groupe de Structure</h1>

<form action="grp-structure.php" method="post" >
	<?php if( $mode == GS_MODIF ) : ?>
		<p>Modification du groupe <strong><?php echo secuhtml($groupe['nom']) ?></strong> : 
		<input type="text" name="nom" value="<?php echo secuhtml($groupe['nom']) ?>" />
		<input type="hidden" name="idm" value="<?php echo (int)($groupe['id']) ?>" />
	<?php else : ?>
		<p>Créer un nouveau groupe : <input type="text" name="nom" value="" />

	<?php endif ?>
	<input type="submit" name="ok" value="Valider" />
	<?php if( $mode == GS_MODIF ) : ?>
	<a href="grp-structure.php" >Annuler</a>
	<?php endif ?>
	</p>
</form>


<?php if( $do = fetch($lsg) ) : ?>
<table>
	<?php do{ ?>
	<tr>
		<td><?php echo secuhtml($do['nom']) ?></td>
		<td><a href="grp-structure.php?idm=<?php echo (int)($do['id']) ?>" >Modifier</a></td>
		<td><a href="<?php echo 'grp-structure.php?ids='.(int)$do['id'] ?>" onclick="return confirm('Voulez vous vraiment supprimer ce groupe ?');" >Supprimer</a></td>
		<td><a href="<?php echo 'grp-structure-str.php?id='.(int)$do['id'] ?>" >Structure dans le groupe</a></td>
	</tr>
	<?php }while($do = fetch($lsg) ); ?>
</table>
<?php else : ?>
	<p>Aucun groupe n'a été crée.</p>
<?php endif ?>
