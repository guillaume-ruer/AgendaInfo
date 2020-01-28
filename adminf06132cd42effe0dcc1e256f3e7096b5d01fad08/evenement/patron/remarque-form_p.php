<h1>Envoyer un mail de remarque</h1>

<?php pmess(); ?>

<?php if( !$traitement  ) : ?>


<form action="remarque-form.php" method="post" >

	<fieldset>
		<legend>Destinataire(s)</legend>
	<p>Adresse Email : <input id="mail" type="text" name="mail" value="<?php echo $mail ?>" size="50" /><br />
		Mettre ces emails pour des structures du LEI : <strong>slafaye@crt-limousin.fr, 
		achatenet@crt-limousin.fr</strong> et enlever celui de la structure</p>

	<p>Sujet : <input type="text" name="sujet" value="<?php echo $sujet ?>" size="50" /></p>
	</fieldset>

	<fieldset>
		<legend>Message</legend>
		<p>Phrase préconstruite ( positionner le curseur à l'endroit voulu dans le champ texte, et 
		choisissez la phrase a insérer. Voir la liste dans "Evénement -&gt; Phrases préconstruites" ) : <br />
		<select onchange="insert_phrase(tabphrase[this.value], 'rq_contenu');" >
		<option>Choisissez</option>
		<?php foreach($tab_phrase as $p ) : ?>
			<option value="<?php echo $p['id'] ?>" ><?php echo $p['dim'] ?></option>
		<?php endforeach ?>
		</select>
		</p>

		<p><textarea id="rq_contenu" name="rq_contenu" rows="40"  cols="100" ><?php echo $rq_contenu ?></textarea></p>

		<p><input type="submit" name="ok" value="Ok !" /></p>

		<script type="text/javascript" >
			tabphrase = new Array(); 
			<?php foreach( $tab_phrase as $p ) : ?>
				tabphrase[<?php echo $p['id'] ?>] = "<?php echo chaine_javascript($p['phrase']) ?>"; 
			<?php endforeach ?>
		</script>
	</fieldset>
</form>

<?php endif ?>
