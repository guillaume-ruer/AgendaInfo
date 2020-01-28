<h1><?php echo $intitule; ?> événement </h1>

<?php pmess(); ?>

<?php if( $affiche_remarque ) : ?>
	<p><a href="remarque-form.php?okrq=1&amp;ide=<?php echo $event->acc_id() ?>" >Envoyer une remarque</a></p>
<?php endif ?>

<p><a href="event-form.php?id_maj=<?php echo $event->acc_id() ?>" >Ré-ouvrir l'événement</a></p>

<?php if($affiche_formulaire) : ?>
<form action="event-form.php" method="post" id="form" enctype="multipart/form-data" >
  
  <script type="text/javascript" >


$(function(){
	$('.aide').hide(); 
}); 

function aide(id)
{
	$('#'+id).toggle(); 	
}

</script>

<fieldset>
	<p><strong>Thème</strong> : <select id="sym" name="sym" onchange="imgtheme()" >
	<?php while($sym = $symbole->parcours() ) :  ?>
		<option value="<?php echo $sym->id; ?>" <?php echo ($sym->id == $event->acc_categorie()->acc_id() ) ? 'selected="selected"' : '' ?> >
		<?php echo $sym->nom; ?>
		</option>
	<?php endwhile; ?>
	</select>
	<img id="imgsym" src="<?php echo RETOUR.'img/symboles/'.$event->acc_categorie()->acc_img() ?>" alt="image" />
	</p>

	<p id="aide_theme" class="aide info_event" >Le th&egrave;me permet de mettre l'&eacute;vènement dans un flux th&eacute;matique.  </p>
	


	<div <?php if(!droit(GERER_UTILISATEUR) ) : ?>id="aide_symbole" class="aide"<?php endif ?> >

	<script type="text/javascript" >
	function imgtheme()
	{
		var idi = document.getElementById("sym").value; 
		req(traitement_img_theme, 'image_theme.php?idi='+idi );
	}

	imgtheme();
	</script>


	</div>
</fieldset>


<fieldset>
	<script type="text/javascript" >
	function myprefix()
	{
		var pre = document.getElementById('prefixe').value; 
		document.getElementById('titre').value = pre+' '+document.getElementById('titre').value;
	}
	</script>

	<p><strong>Titre</strong> 
	<input id="titre" type="text" name="titre" value="<?php ps( $event->acc_titre() )?>" size="55"  />
	</p>

	<p <?php if( !droit(GERER_UTILISATEUR) ) : ?>id="option_titre" class="aide"<?php endif ?> >
	<select onchange="javascript:myprefix()" id="prefixe" >
		<option value="" >Insérer un préfixe</option>
		<?php while($pre = $prefixe->parcours() ) : ?>
			<option value="<?php echo $pre->prefixe ?>" ><?php echo $pre->prefixe ?></option>
		<?php endwhile ?>
	</select>

	<input type="button" value="Sélection en minuscule" onclick="selmin('titre')" />
	
	</p>

	<p id="aide_titre" class="aide info_event" >Le titre (<?php echo MIN_TITRE ?> caractères minimum) doit être explicite, important pour les moteurs de recherche, &quot;Spectacle de marionnettes : La forêt endormie&quot; par exemple.  </p>

<p><a onclick="aide('aide_titre')" >Aide</a> - 
	<?php if( !droit(GERER_UTILISATEUR) ) : ?><a onclick="aide('option_titre')" >Option</a><?php endif ?></p>
    </fieldset>

<fieldset>
	<p><strong>Description</strong></p>

	<p id="aide_desc" class="aide info_event" >Faites court, <?php echo MIN_DESC ?> caractères minimum, Google affiche une ligne et demie, mettez en premier l'horaire, 
	puis le lieu dans la commune (salle des fêtes...), le coût, le type de public... </p>

	

	<p><textarea id="description" name="desc" rows="7" cols="50" onkeyup="nbcar()" ><?php ps( $event->acc_desc() ) ?></textarea></p>
    <p>Nombre de caractères (<em id="app" ></em>) : <span id="nbcar" ></span></p>
<p><a onclick="aide('aide_desc')" >Aide</a> - 
	<?php if(!droit(GERER_UTILISATEUR) ) : ?>
	<a onclick="aide('option_description')" >Option</a></p>
	<?php endif ?>

	<p <?php if(!droit(GERER_UTILISATEUR) ) : ?>id="option_description" class="aide"<?php endif ?> >
	Optimisation automatique de la description (ex : euro devient €) : 
	  <input type="button" value="Auto" onclick="homogene()" /><br />
	Mettre en minuscule le texte dans la sélection : 
	  <input type="button" value="minuscule" onclick="selmin('description')" />
	</p>

	<script type="text/javascript" >
	nbcar(); 
	</script>

</fieldset>

<fieldset>
<p><strong>Date(s)</strong><br />Nous limitons la saisie à <?php echo MAX_NB_DATE ?> jours maximum, une saisie ne peut être affichée sur une année. Cela peut être un ou plusieurs jours par semaine.
	</p>
<div id="output-flat" style="float:left;width:300px" ></div>

	<div >

<div id="aide_date" class="aide" >
			<p>Pour sélectionner plusieurs dates, cliquez sur la date de départ, puis sur SHIFT + clic sur la date de fin.</br>
			Vous pouvez répéter l'opération à condition d'utiliser CTRL + clic pour la date de départ suivante, sinon la sélection est réinitialisée.
			</p>
			<p>Vous pouvez aussi utiliser CTRL + clic pour sélectionner/dé-sélectionner les dates une à une.</p>
			<p>Astuce pour gagner en nombre de clic : si l'événement dure un mois sauf les samedis, sélectionnez toute la période avec 
			MAJ + clic, puis dé-séléctionnez les samedis en utilisant CTRL + clic.</p>
			<p>Vous pouvez changer de mois en utilisant la molette de votre souris lorsqu'elle est au-dessus du calendrier.</p>
			<p class="info_event stop_float" >S&eacute;lectionnez uniquement le ou les jours de l'&eacute;vènement, 
				si c'est tous les samedis sur un an : cochez-les, si c'est pendant un mois : cochez tous les jours de ce mois...  
			</p>
		</div>
	</div>

	<p><input name="date" type="hidden" id="date" value="<?php echo implode(',' , $event->acc_tab_date() ); ?>" /></p><div class="stop_float" ></div><a onclick="aide('aide_date')" >Aide</a>
</fieldset>

<fieldset>
	<p><strong>Communes(s)</strong></p>

	<div>
		<?php $ch_lieu->aff() ?>
	</div>
</fieldset>

<fieldset id="event_form_contact" >

<p><strong>Contact</strong></p>

	<p id="aide_contact" class="aide info_event" >Si le contact comporte une erreur, modifiez-le en allant dans 
		&quot;Liste&quot; &quot;Structure&quot; dans le menu de gauche.</p>

	<select id="contact" name="contact" onchange="javascript:info_contact();" >
		<option >Choisissez le contact</option>
		<?php while($co = $contact->parcours() ) : ?>
		<option value="<?php echo $co->id; ?>" <?php if($co->id == $event->acc_contact()->acc_id() ):?>selected="selected"<?php endif ?> >
			<?php echo "$co->nom, $co->titre ($co->ville)"; ?>
		</option>
		<?php endwhile; ?>
	</select>

	<p id="info_contact" ></p>

	<script type="text/javascript" >
	function info_contact()
	{
		var ida = document.getElementById("contact").value; 
		req(traitement, "antenne.php?ida="+ida ); 
	}

	$(function() { 
		setTimeout( info_contact, 500 ); 
	} );
		
	</script>

<p><a onclick="aide('aide_contact')" >Aide</a></p>
</fieldset>

<fieldset>
		<p><strong>Visuel</strong></p>
	
	<p>Format JPG uniquement, taille <?php echo IMAGE_LARGEUR ?>*<?php echo IMAGE_HAUTEUR ?>px (rapport A4 format portrait), vous pouvez envoyer un visuel plus grand, il sera redimensionné.<br />
	  <strong>Attention : ne pas mettre d'image au format paysage, elle va être déformée !</strong>
<?php $image->aff() ?>

</fieldset>



<fieldset>
	<p><strong>Historique de l'événement (date de saisie, de modification,...)</strong></p>

	<?php if( empty($historique) ) : ?>
		<p>L'historique est vide.</p>
	<?php else : ?>
	<div  id="fen_historique" >
		<table  class="table_defaut" >
			<tr>
				<th>Date</th>
				<th>Utilisateur</th>
				<th>Commentaire</th>
				<th>Evénement </th>
			</tr>
			<?php foreach($historique as $com ) : ?>
			<tr>
				<td><?php echo $com['date'] ?></td>
				<td><?php echo $com['pseudo'] ?></td>
				<td><?php echo $com['com'] ?></td>
				<td><h3><?php echo $com['titre'] ?></h3>
					<p><?php echo $com['desc'] ?></p>
					<p><?php echo $com['event'] ?></p>
				</td>
			</tr>
			<?php endforeach ?>
		</table>
	</div>
	<?php endif ?>
</fieldset>
<?php if( $event->acc_id_externe() != 0 ) : ?>
<fieldset>
<p>
Commentaire(s) (uniquement pour les modérateurs ) : <textarea name="com" rows="5" cols="50" ><?php echo $commentaire ?></textarea></p>

</fieldset>


<fieldset>
	<legend>Alerte Lei</legend>
	<table class="table_defaut" >
	<tr>
		<th>Titre</th>
		<th>Type</th>
		<th>Cause</th>
		<th>Date</th>
		<th>Résolue</th>
	</tr>

	<?php while ( $a = $alerte->parcours() ) : ?>
	<tr>
		<td><?php echo $a->acc_titre() ?></td>
		<td><?php echo $TYPE_ALERTE[ $a->acc_type() ] ?></td>
		<td><?php echo nl2br($a->acc_cause() ) ?></td>
		<td><?php echo madate($a->acc_time() ) ?></td>
		<td><label><input type="checkbox" name="tab_alerte[]" value="<?php echo $a->acc_id() ?>" checked="checked" />Résolu</label></td>
	</tr>
	<?php endwhile ?>
	</table>
</fieldset>
<?php endif ?>

<p><strong>Etat de cette saisie pour la diffusion</strong><br />
  (l'état actif est accessible après plusieurs saisies)<br />
<select name="etat" >
  <?php foreach($tab_etat as $id => $etat ) : ?>
  <option value="<?php echo $id; ?>" <?php echo ($id == $event->acc_etat() ) ? ' selected="selected" ' : ''; ?> 
		<?php echo (!droit(MODIF_ETAT) AND $id==1 ) ? 'disabled="disabled"' : ''  ?> ><?php echo $etat; ?></option>
	<?php endforeach; ?>
	</select>
</p>


<p>
<input type="hidden" name="id_maj" value="<?php echo $event->acc_id(); ?>" />
<input type="hidden" name="stq_date_maj" value="<?php echo $event->acc_date_maj() ?>" />
<input type="submit" name="ok" id="lebouton" value="Envoi pour diffusion" />
<?php if(droit(GERER_UTILISATEUR) AND ($event->acc_id()!=0) ) : ?>
	<input type="submit" name="okrq"  id="lebouton" value="Enregistrer les modifications et envoyer une remarque" />
	</p>

	<p><a href="remarque-form.php?ide=<?php echo $event->acc_id() ?>" >Ne pas enregistrer les modifications 
		et envoyer un mail de remarque</a></p>
<?php else : ?>
</p>
<?php endif ?>

</form>

<script type="text/javascript">
<?php
$js_date = array(); 
foreach($event->acc_tab_date() as $d )
{
	$d = explode('-', $d);
	$js_date[] = sprintf('%04d%02d%02d', (int)$d[0], (int)$d[1], (int)$d[2] ); 
}
?>

// Flat calendard setup
JC = Calendar.setup({
	cont : 'output-flat',
	selectionType : Calendar.SEL_MULTIPLE,
	animation:false, 
	onSelect : function () { 
		var chaine='';
		var d; 
		for( var i in this.selection.getDates() )
		{
			d = this.selection.getDates()[i]; 
			chaine += Calendar.printDate(d, '%Y-%o-%e')+','; 
		}

		$('#date').val(chaine); 
	}, 
	selection : [ <?php echo implode(',',$js_date); ?> ], 
	min : <?php echo date('Ymd') ?> 
});

</script>

<?php endif ?>
