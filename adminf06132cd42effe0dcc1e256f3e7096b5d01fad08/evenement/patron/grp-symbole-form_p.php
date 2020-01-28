<h1>Edition d'un groupe de symbole</h1>

<p><a href="grp-symbole.php" >Retour à la liste des groupes de symbole</a></p>
<?php pmess() ?>

<form action="grp-symbole-form.php" method="post" >

<p><label>Nom : <input type="text" name="nom" value="<?php echo $gs->nom ?>" /></p>

<p><input type="hidden" value="<?php echo $gs->id ?>" name="id" />
<input type="submit" name="ok" value="Ok !" /></p>

</form>


