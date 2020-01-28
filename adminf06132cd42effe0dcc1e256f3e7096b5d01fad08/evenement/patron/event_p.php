<h1>Liste des Ev&eacute;nements</h1>

<form action="event.php" method="post" >

<p>
	<img src="<?php echo RETOUR; ?>jscalendar/img.gif" alt="" id="DateDeb_trigger" />
	<input name="date" size="12" type="text" id="date" value="<?php echo $date_champ; ?>" style="color:black" />
	<script type="text/javascript" >
	Calendar.setup({
		inputField:"date",     // id of the input field
		ifFormat:"%d/%m/%Y",      // format of the input field
		button:"DateDeb_trigger",  // trigger for the calendar (button ID)
		singleClick:true
	});
	</script>

	Etat : <select name="etat" >
	<?php foreach($tab_etat as $num => $nom ) :  ?>
		<option value="<?php echo $num; ?>" <?php selected($etat==$num) ?> ><?php echo $nom ?></option>
	<?php endforeach ?>
	</select>

	<?php if(droit(GERER_LEI ) ) : ?>
		Lei : <select name="lei" >
			<?php foreach($tab_lei as $num => $nom ) :  ?>
				<option value="<?php echo $num ?>" <?php echo $lei == $num ? $sel : '' ?> ><?php echo $nom ?></option>
			<?php endforeach ?>
		</select>
	<?php endif ?>

	<input type="submit" name="ok" value="Lancer le tri" />
	<a href="#" id="bt-plus-option" >+ options</a>
</p>

<div id="plus-option" >

<label for="rech" >Recherche (titre et description) : </label><input id="rech" type="text" name="rech" value="<?php echo $rech ?>" />
<br />

<?php $ch_lieu->label ?>
<?php $ch_lieu->champ ?>
<br />
<label for="groupe_lieu" >Groupe de lieu : </label><select id="groupe_lieu" name="groupe_lieu" id="groupe_lieu" onchange="javascript:deroulant_defaut('lieu');">
<?php foreach($tab_groupe_lieu as $do ){ extract($do); ?>
	<option value="<?php echo $value; ?>" <?php echo $s; ?> ><?php echo $nom ?></option>
<?php } ?>
</select>
<br />

<label for="theme" >Theme : </label><select id="theme" name="theme" >
	<option value="" >Choisir un thème</option>
<?php while($sg = $reqtheme->parcours() ) : ?>
	<option value="<?php echo $sg->id ?>" <?php if($sg->id == $theme ) : ?>selected="selected"<?php endif ?> ><?php echo $sg->nom ?></option>
<?php endwhile ?>
</select>
<br />

<?php if( droit(GERER_EVENEMENT) ) : ?>
<label for="struct" >Structure : </label><select id="struct" name="struct" >
	<option value="" >Choisir une structure</option>
	<option value="-1" <?php selected($struct == -1) ?> >Evénement sans contact</option>
	<?php while( $str = $ls_structure->parcours() ) : ?>
		<option value="<?php $str->aff_id() ?>"
			<?php selected( $str->acc_id() == $struct ) ?> > <?php $str->aff_nom() ?></option>
	<?php endwhile ?>
	</select>
<br />
<label for="grpstr" >Groupe de structure : </label><select id="grpstr" name="grpstr" >
	<option value="" >Groupe de structure</option>
	<?php while( $gs = fetch($grp_structure) ) : ?>
		<option value="<?php echo (int)$gs['id'] ?>" <?php selected($gs['id']==$grpstr) ?> ><?php echo secuhtml($gs['nom']) ?></option>
	<?php endwhile ?>
</select>
<?php endif ?>

</div>

</form>
</div>

<?php $ls->acc_pagin()->affiche() ?>

<p>Vert : en diffusion, jaune : pas en diffusion, rouge : supprimé.</p>

<div class="table_defaut" >
<table>
	<tr>
		<th></th>
		<th>Date</th>
		<th>Titre</th>
		<th>Description</th>
		<th>Structure</th>
		<th>Lieu</th>
		<?php if( droit(GERER_EVENEMENT) ) : ?>
		<th>Etat</th>
		<?php endif ?>
		<th colspan="2" >Action</th>
	</tr>
<?php while($do = $ls->parcours() ) :  ?>
	<tr class="xform event <?php echo $tab_class_etat[ $do->acc_etat() ] ?>" data-id="<?php echo $do->acc_id() ?>"
			data-action="ajax/xform-event.php" >
		<td>
			<span class="edite" data-name="symbole" data-type="xselect" data-option="symbole" ><?php $do->acc_categorie()->aff() ?></span>
			<br />
			<?php if( $do->acc_image() ) : ?>
			<img src="<?php echo C_EVENT_IMAGE.$do->acc_image() ?>" />
			<?php endif ?>
		</td>
		<td><?php $do->aff_date() ?></td>
		<td class="titre edite" data-name="titre" ><?php $do->aff_titre() ?></td>
		<td class="description edite" data-name="description" ><?php $do->aff_desc(FALSE) ?></td>
		<td>
			<?php if( !($do->acc_contact()->acc_structure()->acc_actif() == structure::ACTIF) ) : ?>
				<span style="color:red;font-weight:bold" >
					<?php if( $do->acc_contact()->acc_id() == 0 ) :?>
						Pas de contact renseigné
					<?php elseif(  $do->acc_contact()->acc_structure()->acc_actif() == structure::INACTIF ) : ?>
						Structure inactive 
					<?php else: ?>
						Structure en attente
					<?php endif ?>
				</span><br /><br />
			<?php endif ?>
			<a href="<?php echo C_ADMIN.'structure/str.php?ids='.$do->acc_contact()->acc_structure()->acc_id() ?>" target="_blank" ><?php $do->acc_contact()->acc_structure()->aff_nom() ?></a>
		</td>
		<td>
			<?php $virg=''; foreach($do->acc_tab_lieu() as $v ) :  
				echo $virg.' '.$v->acc_nom(); 
				if( empty($virg) ) $virg=','; 
			endforeach ?>
		</td>
		<td>
			<?php foreach(evenement::$TAB_ETAT as $num => $nom ) :  ?>
				<?php $bloq = $num == evenement::ACTIF && !droit(MODIF_ETAT) ? 'disabled="disabled"' : ''; ?>

				<label style="white-space: nowrap; <?php if($bloq) echo 'color:#777777' ?>" ><?php echo $nom ?> : <input class="ch-etat" type="radio" 
					name="etat[<?php echo $ls->acc_num() ?>]" 
					value="<?php echo $num ?>" <?php checked($num == $do->acc_etat() ) ?> 
					<?php echo $bloq ?>
				/>
				</label>
				<br />
			<?php endforeach ?>
		</td>
		<td><a href="event-form.php?id_maj=<?php ps( $do->acc_id() ) ?>" >Modifier</a></td>
	</tr>

<?php endwhile  ?>
</table>
</div>

<?php $ls->acc_pagin()->affiche() ?>

<script>
$(function(){
	$('#plus-option').hide(); 

	$('#bt-plus-option').click(function(){
		var $po = $('#plus-option'); 
		!$po.is(':visible') ? $po.slideDown() : $po.slideUp();  
	}); 

	(function(){
		var tab_etat = <?php echo json_encode($tab_class_etat) ?>; 

		$('.ch-etat').click(function(){
			var $radio = $(this); 
			var $ligne = $radio.closest('tr'); 

			$.post('ajax/etat-event.php', {
					'id' : $ligne.attr('data-id'), 
					'etat' : $radio.val()
				}, 
				function(data){
					switch(data)
					{
						case '0' :
							for( var nomclass in tab_etat )
							{
								$ligne.removeClass(tab_etat[nomclass]); 
							}

							$ligne.addClass(tab_etat[ $radio.val() ]);
							$radio.prop('checked', true); 				
						break; 
						case '2' : 
							alert('Vous n\'avez pas les droits nécessaire'); 
						break; 
						default :
							alert('Une erreur du serveur c\'est produite. '+data); 
					}
			}); 

			return false; 
		});

	})();
}); 

XSELECT_OPTION['symbole'] = [
<?php foreach($tabtheme as $theme ) : ?>
	{ 
		'label' : "<?php echo addslashes('<img src="'.RETOUR.'img/symboles/'.$theme['img'].'" width="32" height="32" />').' '.$theme['nom'] ?>", 
		'repl' : "<?php echo addslashes('<img src="'.RETOUR.'img/symboles/'.$theme['img'].'" title="'.$theme['nom'].'" width="'.$theme['width'].'" height="'.$theme['height'].'" />') ?>", 
		'value' : "<?php echo $theme['id'] ?>"
	},
<?php endforeach ?>
];
</script>

