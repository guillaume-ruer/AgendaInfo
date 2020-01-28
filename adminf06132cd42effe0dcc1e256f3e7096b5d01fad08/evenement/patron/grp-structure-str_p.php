<h1>Structure dans le groupe : <?php ps($grp['nom']) ?></h1>

<p><a href="grp-structure.php" >Retour à la liste des groupe</a></p>

<?php if( $valide ) : ?>

	<p>Structure associé au groupe avec succès.</p>

<?php else : ?>

<form action="grp-structure-str.php" method="post" >

	<?php $form->aff() ?>
	
	<p><input type="hidden" name="id" value="<?php echo (int)$grp['id'] ?>" />
	<input type="submit" name="ok" value="Valider" />
	</p>
</form>

<?php endif ?>
