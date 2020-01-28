<h1>Editition d'un lieu</h1>

<p><a href="lieu.php" >Retour</a></p>


<?php if( $traitement ) : ?>

	<p>Le traitement a été pris en compte.</p>

	<p><a href="lieu.php" >Retour à liste des lieux</a></p>

	<p><a href="<?php echo NOM_FICHIER ?>?id=<?php echo $ville->acc_id() ?>" >Retour au formualaire du lieu</a></p>

<?php else :  ?>
<form action="<?php echo NOM_FICHIER ?>" method="post" enctype="multipart/form-data" >
	<fieldset >
		<legend>Base</legend>

		<p>Nom : <input type="text" name="nom" value="<?php echo $ville->acc_nom() ?>" /></p>
		<p>Département : 
		<select name="dep" >
			<?php foreach(departement::$TAB_DEP as $num => $nom ) : ?>
				<option value="<?php echo $num ?>" <?php selected($num==$ville->acc_dep()->acc_num() ) ?> >
					<?php echo $nom.' ('.$num.')' ?>
				</option>
			<?php endforeach ?>
		</select>
		</p>
		<p>Code postale : <input type="text" name="cp" value="<?php echo $ville->acc_cp() ?>" size="3" /></p>
		<p>Descriptif (n'est pas encore visible) : <textarea name="desc" rows="7" cols="70" ><?php echo $ville->acc_desc() ?></textarea></p>
		<p>Coordonnées géographique en décimal (ex: latitude:45,958333, longitude:1,400833) : <br />
		Latitude : <input type="text" name="lat" value="<?php echo $ville->acc_lat() ?>" /><br />
		Longitude : <input type="text" name="long" value="<?php echo $ville->acc_long() ?>" /> 
		</p>
	</fieldset>

	<fieldset >
		<legend>Liens</legend>

		<p>Site internet : <input type="text" name="site" value="<?php echo $ville->acc_site() ?>" /></p>
		<p>Facebook : <input type="text" name="fb" value="<?php echo $ville->acc_facebook() ?>" /></p>
		<p>Wikipédia : <input type="text" name="wp" value="<?php echo $ville->acc_wikipedia() ?>" /></p>
	</fieldset>

	<fieldset >
		<legend>Téléphone</legend>

		<p>Téléphone mairie : <input type="text" name="tel_mairi" value="<?php echo $ville->acc_mairie() ?>" /></p>
		<p>Téléphone office du tourisme : <input type="text" name="tel_ot" value="<?php echo $ville->acc_office() ?>" /></p>
	</fieldset>

	<fieldset>
		<legend>Image</legend>

		<p>L'image sur la page ville, au dessus de ses informations.</p>
		<?php $image->aff() ?>
	</fieldset>

	<fieldset>
		<legend>Groupe de lieux</legend>

		<select name="grp[]" multiple="yes" size="15" >
		<?php while( $g = $grp->parcours() ) : ?>
			<option value="<?php echo $g->acc_id() ?>" <?php selected( in_array($g->acc_id(), $ville->acc_tab_grp() ) ) ?> >
				<?php echo $g->acc_nom() ?>
			</option>
		<?php endwhile ?>
		</select>
	</fieldset>


	<p><input type="hidden" name="id" value="<?php echo $ville->acc_id() ?>" />
		<input type="submit" name="ok" value="Ok !" />
	</p>
</form>

<?php endif ?>
