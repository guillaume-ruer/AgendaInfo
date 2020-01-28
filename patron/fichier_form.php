
<p><label>Fichier <?php if(!empty($nom) ) : ?>(remplacera l'image actuel)<?php endif ?> : <input type="file" name="fichier_<?php echo $id ?>" /></label>
<input type="hidden" name="max_file_size" value="99999999" />
</p>
<?php if( !empty($nom) ) : ?>
	<input type="hidden" name="fichier_nom_<?php echo $id ?>" value="<?php echo $nom ?>" />
	<p>Actuel : <img src="<?php echo $dos.$nom ?>" /></p>
	<p><label>Suppression : <input type="checkbox" name="fichier_sup_<?php echo $id ?>" /></label></p>
<?php endif ?>
