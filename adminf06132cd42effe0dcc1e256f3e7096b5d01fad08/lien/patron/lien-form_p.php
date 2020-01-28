<h1>Editer un lien</h1>

<?php if( $valide ) : ?>

	<p>Votre demande a été prise en compte.</p>
	<p><a href="lien.php?grp=<?php ps( $lien->acc_type() ) ?>" >Retour</a></p>

<?php else : ?>

<form action="<?php echo NOM_FICHIER ?>" method="post" enctype="multipart/form-data" >
<p>Type : <select name="type" >
	<?php while( $lg = $lien_grp->parcours() ) : ?>
	<option value="<?php $lg->aff_id() ?>" <?php selected($lg->acc_id() == $lien->acc_type() ) ?> ><?php $lg->aff_nom() ?></option>
	<?php endwhile ?>
</select></p>
<p>Titre : <input type="text" name="titre" value="<?php $lien->aff_titre() ?>" /></p>
<p>Url : <input type="text" name="url" value="<?php $lien->aff_url() ?>" /></p>
<p>Ville : <select name="lieu[]" multiple="yes" size="10" >
	<?php while( $v = $ville->parcours() ) : ?>
	<option value="<?php $v->aff_id() ?>" <?php selected( in_array($v->acc_id(), $lien->acc_lieu() ) ) ?> ><?php $v->aff_nom() ?></option>
	<?php endwhile ?>
</select>
</p>

<p>Groupe de lieu : <select name="grp_lieu[]" multiple="yes" size="10" >
	<?php while( $g = $grp->parcours() ) : ?>
	<option value="<?php echo $g->acc_id() ?>" <?php selected( in_array($g->acc_id(), $lien->acc_grp_lieu() ) ) ?> >
		<?php echo $g->acc_nom() ?>
	</option>
	<?php endwhile ?>
</select>
</p>

<p>Image (il n'y a pas de vérification de type, ni de taille) : 
<input type="file" name="img" />
<input type="hidden" name="max_file_size" value="9999999" /><br />
<?php $lien->aff_img() ?>
<label>Cochez pour supprimer l'image : <input type="checkbox" name="sup" /></label>
</p>

<p><input type="hidden" name="id" value="<?php $lien->aff_id() ?>" />
<input type="submit" name="ok" value="Ok !" /></p>
</form>

<?php endif ?>
