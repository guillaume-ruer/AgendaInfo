<h1>Liste des liens</h1>

<form action="<?php echo NOM_FICHIER ?>" method="post" >

<table>
<?php while( $l = $lien->parcours() ) : ?>
	<tr>
		<td><?php $l->aff_titre() ?></td>
		<td><?php $l->aff_img() ?></td>
		<td><?php $l->aff_url() ?></td>
		<td>[<a href="lien-form.php?id=<?php $l->aff_id() ?>" >modif</a>]</td>
		<td><input type="checkbox" name="ids[]" value="<?php $l->aff_id() ?>" /></td>
	</tr>
<?php endwhile ?>
</table>
<p>Valider la suppression des liens séléctionnés : <input type="submit" name="oks" value="Suppression" />
<input type="hidden" name="grp" value="<?php echo $grp ?>" /></p>
</form>

