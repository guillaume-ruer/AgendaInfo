<h1>Edition d'un symbole</h1>

<p><a href="symbole.php" >Retour</a></p>

<?php pmess() ?>

<form action="symbole-form.php" method="post" enctype="multipart/form-data" >
	<div>
		<?php $s->aff() ?>
		<div style="display:inline-block" >
		<p><label>Nom : <input type="text" name="nom" value="<?php echo $s->nom ?>" size="40" /></label></p>

		<p>Groupe de symboles : <select name="groupe" >
		<?php while($g = $lsgroupe->parcours() ) : ?>
			<option value="<?php echo $g->id ?>" <?php if($g->id == $s->id_groupe ) : ?>selected="selected" <?php endif ?> >
			<?php echo $g->nom ?></option>
		<?php endwhile ?>
		</select>
		</p>
		</div>
	</div>

	<p><strong>Remplacer l'image</strong><br />
	Redimensionner l'image envoyé : <input type="checkbox" name="redim" <?php if($redim) : ?>checked="checked"<?php endif ?> /><br />
	Largeur : <input type="text" size="4" name="width" value="<?php echo $width ?>" />px<br />
	Hauteur : <input type="text" size="4" name="height" value="<?php echo $height ?>" />px<br />
	<input type="file" name="img" />
	<input type="hidden" name="max_file_size" value="99999999" />
	<br />
	</p>

	<p><input type="hidden" name="id" value="<?php echo $s->id ?>" />
	<input type="submit" name="ok" value="Ok !" /></p>
</form>
