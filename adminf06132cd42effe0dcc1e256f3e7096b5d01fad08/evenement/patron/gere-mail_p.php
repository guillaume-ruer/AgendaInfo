<h1>Gestion du modèles des mail de remarque</h1>

<?php pmess() ?>

<form action="gere-mail.php" method="post" >

<p>Sujet du mail par défaut : <input type="text" name="sujet" value="<?php echo $sujet ?>" size="50" /></p>

<p>Le corps du message de remarque par défaut sera composé de 3 (ou 4) parties :</p>
<ul>
	<li>Le haut du mail (modifiable ci-dessous).</li>
	<li>D'un résumé de l'évenement dans l'état actuel. (Géré de façon automatique.)</li>
	<li>Dans le cas de la demande d'enregistrement avant l'envoie d'un mail de remarque, 
		un résumé de l'évenement avant modification. (Géré de façon automatique.)</li>
	<li>Le bas du mail (modifiable ci-dessous).</li>
</ul>

<p>Haut du mail : <br /><textarea name="haut" rows="10" cols="80" ><?php echo $haut ?></textarea></p>
<p>Bas du mail : <br /><textarea name="bas" rows="10" cols="80" ><?php echo $bas ?></textarea></p>
<p><input type="submit" name="ok" value="Ok !" /></p>
</form>
