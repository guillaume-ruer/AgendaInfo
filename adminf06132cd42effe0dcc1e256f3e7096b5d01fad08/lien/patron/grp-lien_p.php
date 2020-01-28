<h1>Groupes de liens</h1>

<form action="<?php echo NOM_FICHIER ?>" method="post" >
<p><?php if($lg->acc_id() == 0 ) : ?>Nouveau : <?php else : ?>Modifier : <?php endif ?>
<input type="text" name="nom" value="<?php $lg->aff_nom() ?>" size="40" />
<input type="hidden" name="id" value="<?php $lg->aff_id() ?>" />
<input type="submit" name="ok" value="Ok !" />
<?php if($lg->acc_id() != 0 ) : ?><a href="<?php echo NOM_FICHIER ?>" >Annuler</a><?php endif ?>
</p>
</form>

<form action="<?php echo NOM_FICHIER ?>" method="post" >

<table>
	<?php while( $lg = $lien_grp->parcours() ) : ?>	
		<tr>
			<td><?php $lg->aff_nom() ?></td>
			<td>[<a href="<?php echo NOM_FICHIER ?>?idm=<?php $lg->aff_id() ?>" >modif</a>]</td>
			<td><input type="checkbox" name="ids[]" value="<?php $lg->aff_id() ?>" /></td>
		</tr>
	<?php endwhile ?>
</table>

<p>Attention : tout les liens contenu dans les groupes supprimé seront également supprimé. 
	<input type="submit" name="oks" value="Valider la suppression" />
</p>
</form>
