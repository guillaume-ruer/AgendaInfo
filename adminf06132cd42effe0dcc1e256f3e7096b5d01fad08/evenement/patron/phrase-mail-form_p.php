<h1>Edition d'une phrase préconstruite </h1>

<?php pmess() ?>

<p><a href="phrase-mail.php" >Retour</a></p>

<?php if($affiche) : ?>


<form action="phrase-mail-form.php" method="post" >

	<p>Diminutif (Le diminutif est visible dans le menu déroulant, il doit bien décrire le contenu du texte qui sera inséré ) : 
		<input type="text" name="dim" value="<?php echo $dim ?>" />
	</p>
	<p>Phrase : <textarea name="phrase" rows="7" cols="70" ><?php echo $phrase ?></textarea></p>
	<p><input type="hidden" name="id" value="<?php echo $id ?>" />
	<input type="submit" name="ok" value="Ok !" /></p>

</form>
<?php endif ?>



