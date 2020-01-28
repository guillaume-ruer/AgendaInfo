<h1>Mon compte</h1>

<p><a href="." >Voir mon compte</a></p>

<?php if($maj) : ?>

<p>La mise à jour à été éfféctué.</p>

<?php else : ?>

<form action="form.php" method="post" >

	<p>Adresse mail : <input type="text" name="mail" value="<?php echo $mail ?>" size="40" /></p>

	<?php if( droit(GERER_EVENEMENT) ) : ?>
	<p><label>Je souhaite recevoir le compte rendu de l'activité du site quotidiennement : 
		<input type="checkbox" name="cr" <?php checked( $cr ) ?> /></label>
	</p>

	<p><label>Je souhaite recevoir des notifications de l'activité des comptes : 
		<input type="checkbox" name="notif" <?php checked( $notif ) ?> /></label>
	</p>

	<?php endif ?>

	<p><input type="submit" name="ok" value="Ok !" /></p>
</form>

<?php endif ?>
