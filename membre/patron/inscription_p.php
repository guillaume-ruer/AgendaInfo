<div class="fixe" >
	<h1>Inscription pour adhérer à l'association Info Limousin</h1>
	<?php pmess() ?>

	<?php if($ins_ouvert ) : ?>

		<?php if( $valide ) : ?>

			<p>Vous êtes désormais inscrits sur la plateforme de diffusion, veuillez suivre les instructions dans l'email de confirmation.</p>

		<?php else : ?>
			
			<p><img src="../../img/tarifs/23-euros.png" width="30%" alt="adhesion-particulier" /> <img src="../../img/tarifs/33-euros.png" width="30%" alt="adhesion-association" /> <img src="../../img/tarifs/53-euros.png" width="30%"  alt="adhesion-societe" /><br />
			  Seule une adhésion (en 2017 : 23, 33 ou 53 euros selon votre statut) ouvre le droit à la diffusion de vos événements.<br />
			Une fois le compte créé, suivez les instructions pour adhérer à l'association Info Limousin pour 365 jours.<br />
			Les communes de la Nouvelle-Aquitaine sont présentes dans la base de données.</p>

			<form action="inscription.php" method="post" >

			  <p>Les champs indiqués d'un <?php echo champ_form::CHAINE_REQUIS ?> sont obligatoires.</p>

				<h2>Coordonnées personnelles</h2>
				<?php $f_perso->aff() ?>

				<h2>Informations de connexion</h2>

				<p>Un email avec un code de confirmation vous sera envoyé à cette adresse.</p>

				<?php $f_connexion->acc('mail')->aff() ?>

				<p>Le mot de passe doit contenir au moins <?php echo INS_MIN_CAR ?> caractères.</p>

				<?php $f_connexion->acc('mdp')->aff() ?>
				<?php $f_connexion->acc('mdp2')->aff() ?>

				<h2>Structure</h2>

				<p>Chaque évènement créé est attaché à un contact d'une structure. Ces informations sont affichées avec l'évènement.</p>
				<?php $f_structure->aff() ?>

				<p>Adresse : </p>
				<?php $f_adresse->aff() ?>

				<p><input type="submit" name="ok" value="Valider l'inscription à la plateforme" /></p>
			</form>

		<?php endif ?>
	<?php endif ?>
    <p><a href="connexion.php" >Retour à la page de connexion</a> - <a href="../index.php" >Retour à l'agenda</a></p>
</div>


