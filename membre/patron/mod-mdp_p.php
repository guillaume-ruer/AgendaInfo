<div class="fixe" >

	<h1>Modification de votre mot de passe</h1>

	<?php pmess() ?>

	<?php if( !$valide) :?>
	<form action="mod-mdp.php" method="post" >
		<?php $modif_mdp->aff() ?>

		<p>Le mot de passe doit contenir au moins <?php echo INS_MIN_CAR ?> caractères.</p>

		<p><input type="submit" name="ok" value="Valider" /></p>
	</form>

	<?php endif ?>

	<p><a href="connexion.php" >Connexion</a></p>
</div>
