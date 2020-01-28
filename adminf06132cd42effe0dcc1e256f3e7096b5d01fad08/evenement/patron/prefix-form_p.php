<h1>Gestion du préfixe</h1>

<?php pmess() ?>

<p><a href="prefix.php" >Retour</a></p>

<form action="prefix-form.php" method="post" >

<p>Préfixe : <input type="texte" name="prefixe" value="<?php echo $prefixe ?>" />
	<input type="hidden" name="idp" value="<?php echo $idp ?>" />
	<input type="submit" name="ok" value="Ok !" />
</p>

</form>
