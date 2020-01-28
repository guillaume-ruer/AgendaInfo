<fieldset>
	<legend>Info</legend>

	<table>
		<?php if(droit(GERER_UTILISATEUR) ) : ?>
		<tr>
			<td><label for="s_numero" >Numéro : </label></td>
			<td><input id="s_numero" type="text" name="s_numero" value="<?php ps( $str->acc_numero() ) ?>" /></td>
		</tr>
		<?php endif ?>
		<tr>
			<td><label for="s_nom" >Nom (obligatoire): </label></td>
			<td><input id="s_nom" type="text" name="s_nom" value="<?php ps( $str->acc_nom() ) ?>" size="40"/></td>
		</tr>
		<tr>
			<td><label for="s_ville" >Commune : </label></td>
			<td>
				<?php $ch_lieu->aff_champ() ?>

	<?php /*	
			<select id="s_ville" name="s_ville" >
			<option value="" >Choisir</option>
			<?php while($v = $ville->parcours() ) : ?>
				<option value="<?php echo $v->id ?>" <?php selected( $v->id==$str->acc_adresse()->acc_ville()->acc_id() ) ?> >
					<?php echo $v->ville ?> (<?php echo $v->dep ?>)
				</option>
			<?php endwhile ?>
			</select>
		*/ ?>
			</td>
		</tr>
		<tr>
			<td><label for="s_addr" >Adresse : </label></td>
			<td><input id="s_addr" type="text" name="s_addr" value="<?php ps( $str->acc_adresse()->acc_rue() ) ?>" size="40"/></td>
		</tr>
		<tr>
			<td><label for="s_mail" >Email (pas diffusé dans l'agenda) : </label></td>
			<td><input id="s_mail" type="text" name="s_mail" value="<?php ps( $str->acc_mail() ) ?>" size="40"/></td>
		</tr>
		<tr>
			<td><label for="s_mail_rq" >Email pour l'envoi de remarques au moment de la saise d'événements. Si vide, l'email renseigné précédement sera utilisé) : </label>
			</td>
			<td><input id="s_mail_rq" type="text" name="s_mail_rq" value="<?php ps( $str->acc_mail_rq() ) ?>" size="40"/></td>
		</tr>

		<tr>
			<td><label for="s_logo" >Logo (largeur : 160 pixels, hauteur : 90 pixels, image jpg) :</label></td>
			<td><input id="s_logo" type="file" name="s_logo" value="" />
			<input type="hidden" name="max_file_size" value="9999999" />
			<?php if( $str->acc_logo() ) : ?>
			<img src="<?php ps( C_IMG.'logos/'.$str->acc_logo() ) ?>" width="160" height="90" alt="" /><br />
			<label>Cocher pour supprimer le logo : <input type="checkbox" name="s_sup_logo" /></label>
			<?php endif ?>
			</td>
		</tr>
		<?php if(droit(GERER_UTILISATEUR ) ) : ?>
		<tr>
			<td>Type (détermine le coùt de l'adhesion) : </td>
			<td><select name="type" >
			<option value="" >Choisir</option>
			<?php foreach($tab_type as $do ) : ?>
				<option value="<?php echo $do[0] ?>" <?php if($do[0] == $str->acc_type() ) : ?>selected="selected"<?php endif ?> >
					<?php echo $do[1] ?>
				</option>
			<?php endforeach ?>  
			</select>
			</td>
		</tr>
		<tr>
			<td><label for="s_conv" >Convention : </label></td>
			<td><input type="text" id="s_conv" name="s_conv" value="<?php $str->aff_conv() ?>" /></td>
		</tr>
		<tr>
			<td><label for="s_actif" >Actif : </label></td>
			<td>
				<select name="s_actif" >
					<?php foreach(structure::$tab_etat as $etat => $netat ) : ?>
						<option value="<?php echo $etat ?>" <?php selected($str->acc_actif() == $etat ) ?> ><?php echo $netat ?></option>
					<?php endforeach ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="s_payant" >Payant : </label></td>
			<td><input id="s_payant" type="checkbox" name="s_payant" <?php if($str->acc_payant() ) : ?>checked="checked"<?php endif ?> 
				value="1" />
			</td>
		</tr>

		<?php endif ?>

	</table>

	<p>Présentation : <br />
	<textarea name="presentation" rows="6" cols="70" ><?php ps( $str->acc_desc() ) ?></textarea>
	</p>
	
</fieldset>


<fieldset>
	<legend>Contact(s)</legend>

	<ul>
		<li>La structure doit avoir au minimun un contact.</li>
		<li>Pour qu'un contact soit créé, au moins l'un de ses champs doit être rempli.</li>
		<li>Attention à la suppression des contacts ; 
			si un événement n'a pas de contact, il n'apparaît pas dans les listes.</li></ul>		
Le premier contact est celui utilisé par défaut.
<?php if( $str->acc_tab_contact() ) : ?>
		
		<?php foreach($str->acc_tab_contact() as $c ) : ?>
  <br />
  <br />
  Titre :
<input type="text" name="titre[]" value="<?php ps( $c->acc_titre() ) ?>" size="40" /><br />
  Téléphone : 
  <input type="text" name="tel[]" value="<?php ps( $c->acc_tel() ) ?>" /><br />
  Site Internet (http://...) : 
  <input type="text" name="site[]" value="<?php ps( $c->acc_site() ) ?>" size="40"/><br />
  <label>Supprimer : 
	<input type="checkbox" name="idcsup[]" value="<?php ps( $c->acc_id() )  ?>" />
  </label>
  <input type="hidden" name="idc[]" value="<?php ps( $c->acc_id() ) ?>" />
  <?php endforeach ?>
	<?php endif ?>

  <fieldset>
		<legend>Nouveau Contact</legend>
		
		<div id="contact" >
		<p>Titre : <input type="text" name="titre[]" value="" size="40" /><br />
		Téléphone : 
		  <input type="text" name="tel[]" value="" /><br />
		Site Internet (http://...) : 
		<input type="text" name="site[]" value="" size="40"/>
		<input type="hidden" name="idc[]" value="" />
		</p>
	</div>
		
		<p id="pajtc" ><input id="ajtc" type="button" value="Autre contact" /></p>

		<script type="text/javascript" >
		$('#ajtc').click(function () { 
			var c = $('#contact p').clone();
			$('input', c ).val(''); 	
			c.insertBefore('#pajtc'); 
		}); 
		</script>
	</fieldset>
</fieldset>

<input type="hidden" name="ids" value="<?php ps( $str->acc_id() ) ?>" />
