<div class="fixe" >

	<h1>Mot de passe oublié</h1>

	<?php pmess() ?>

	<form action="oublie.php" method="post" >
		<p>Indiquer l'adresse email utilisée pour votre compte de diffusion
          <input type="text" name="mail" value="" /><input type="submit" name="ok" value="Ok" />.</p>

		<p>Un code vous sera envoyé vous permettant de modifier votre mot de passe. Ce code est valide 24h.</p>
	</form>

	<p>Rendez vous <a href="mod-mdp.php" >ici</a> si vous avez reçu votre code.</p>

	<p><a href="connexion.php" >Retour à la page de connexion</a> - <a href="../index.php" >Retour à l'agenda</a></p>
</div>
