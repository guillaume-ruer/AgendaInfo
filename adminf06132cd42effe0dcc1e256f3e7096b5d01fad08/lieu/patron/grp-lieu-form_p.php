<h1>Edition d'un groupe de lieu</h1>

<?php if( !$traitement) : ?>
	<form action="grp-lieu-form.php" method="post" >
	<p>Nom : <input type="text" name="nom" value="<?php echo $grp->acc_nom() ?>" /></p>

	<p>Type (definit l'ordre d'apparition) : <select name="ordre" >
		<?php foreach( $tab_ordre as $ordre => $nom ) : ?>
		<option value="<?php echo $ordre ?>" <?php selected($grp->acc_ordre() == $ordre ) ?>  >
		<?php echo $nom ?></option>
		<?php endforeach ?>
		</select>
	</p>

	<?php $ch_lieu->aff() ?>

<?php /*
	<p>Lieux dans le groupe : <select name="lieu[]" multiple="yes" size="25" >
		<?php while ( $l = $lieux->parcours() ) : ?>
			<option value="<?php echo $l->acc_id() ?>" <?php selected(in_array($l->acc_id(), $grp->acc_tab_lieu()  )) ?> >
				<?php echo $l->acc_nom() ?>
			</option>
		<?php endwhile ?>
	</select>
	</p>
*/ ?>

	<p>Numéro (pour les départements) : <input type="text" name="num" value="<?php echo $grp->acc_num() ?>" /></p>

	<p><input type="hidden" name="id" value="<?php echo $grp->acc_id() ?>" />
	<input type="submit" name="ok" value="Ok !" />
	</p>

	</form>

<?php else : ?>
	
	<p>Confirmation du traitement.</p>

<?php endif ?>
