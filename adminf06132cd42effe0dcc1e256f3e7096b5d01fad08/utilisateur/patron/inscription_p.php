<h1>Ouvrir ou fermer l'inscription</h1>

<form action="inscription.php" method="post" >
<?php if( $ins_ouvert ) : ?>
	<p>L'inscription est ouvert.</p>
	<p><input type="submit" name="ok" value="Fermer l'inscription" /></p>
<?php else : ?>
	<p>L'inscription est fermé.</p>
	<p><input type="submit" name="ok" value="Ouvrir l'inscription" /></p>
<?php endif ?>

</form>
