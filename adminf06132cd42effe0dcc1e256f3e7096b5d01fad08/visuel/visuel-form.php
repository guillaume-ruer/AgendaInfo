<?php
include '../../include/init.php'; 
include C_INC.'visuel_class.php'; 
include C_INC.'reqa_class.php'; 
include C_INC.'fonc_upload.php'; 
include C_INC.'fonc_memor.php'; 
include C_INC.'visuel_fonc.php'; 
include C_INC.'mail_fonc.php'; 
include C_INC.'ls_contact_class.php'; 
include C_INC.'lieu_grp_ls.php'; 

// Calendrié javascript 
$modif=FALSE;

http_param(array('id'=>0, 't' => 0) );

if( !isset($VISUEL_CONF[ $t ]) )
{
	page_erreur(); 
}
elseif( !empty($VISUEL_CONF[ $t ]['droit']) && !droit(GERER_VISUEL) )
{
	page_erreur(); 
}

$aff = new visuel($VISUEL_CONF); 

if(empty($id) )
{
	$aff->mut_type($t); 
}
elseif( $aff->init($id) )
{
	if( !droit(GERER_UTILISATEUR) )
	{
		if( !str_droit($aff->acc_id_structure() ) )
		{
			page_erreur(); 	
		}
	}
}
else
{
	mess('Aucun visuel ne correspond à l\'identifiant fourni.'); 
}

$VT = $VISUEL_CONF[ $aff->acc_type() ];

if( $VT['date'] == TRUE )
{
	$dos = RETOUR.'jscalendar/';
	ajt_style('calendar-win2k-cold-1.css', $dos);
	ajt_script('calendar.js', $dos ); 
	ajt_script('calendar-en.js', $dos.'lang/' ); 
	ajt_script('calendar-fr.js', $dos.'lang/' ); 
	ajt_script('calendar-setup.js', $dos ); 
}

if(isset($_POST['ok']) )
{
	$verif = TRUE; 
	http_param(array(
		'titre' => '',
		'texte' => '', 
		'url' => '', 
		'datedeb' => '',
		'datefin' => '',
		'ids' => 0, 
		'idc' => 0, 
	) ); 

	if( !$aff->mut_id_structure($ids) )
	{
		mess('Vous n\'avez pas le droit d\'utiliser cette structure.'); 
		$verif = FALSE; 
	}

	$dos = C_DOS_PHP.$VT['dos']; 

	if( !file_exists($dos) )
	{
		mkdir($dos, 0777, TRUE); 
	}

	if( $img = tcimg('img', $dos, 'gif,jpg,jpeg', $VT['img']['large'], 
		$VT['img']['haut'], TRUE )  )
	{
		$aff->img = $img;
	}
	elseif( empty($aff->img ) )
	{
		$verif=FALSE;
		mess('Il faut envoyer une image pour que le visuel soit pris en compte.');
	}

	if( $verif )
	{
		$aff->titre = $titre; 
		$aff->texte = $texte;
		$aff->url = $url;
		$aff->datedeb = $datedeb;
		$aff->datefin = $datefin;
		$aff->id_contact = $idc; 


		if( $VT['ville'] == VISUEL_VILLE_UNE )
		{
			$ville = isset($_POST['ville']) ? (int)$_POST['ville'] : null; 
			$aff->ville = $ville; 	
		}
		elseif( $VT['ville'] == VISUEL_VILLE_LISTE )
		{
			$ville = isset($_POST['ville']) ? (array)$_POST['ville'] : array(); 
			$grpl = isset($_POST['grpl']) ? (array)$_POST['grpl'] : array(); 
			$aff->tab_ville = array_map('intval', $ville ); 
			$aff->tab_grp_lieu = array_map('intval', $grpl ); 
		}

		$aff->enr(); 
		$mail = 'adhesion@info-limousin.com';
		$sujet = 'Suivi dans l\'administration de l\'outil de diffusion d\'Info Limousin : ajout ou modification visuel';
		$message = 'Un(e) '.$VT['nom'].' a été ajouté ou modifié. 
			Titre : '.$aff->titre.' 
			Description : '.$aff->texte.' 
			-- 
			Contrôleur de l\'outil de diffusion
		';

		mel( $mail, $sujet, $message ); 
		$TRACE->insert('<p>'.secuhtml($mail).'</p><p>'.secuhtml($sujet).'</p><p>'.nl2br(secuhtml($message) ).'</p>', T_MAIL ); 
		$modif = TRUE;
	}
}

// Liste de structure 
$where = ''; 

if( !droit(GERER_UTILISATEUR) )
{
	$where .= ' AND ( structure.id IN(
		SELECT s.id 
		FROM structure s
		LEFT JOIN structure_droit sd
			ON s.id= sd.structure
		WHERE sd.utilisateur = '.ID.'
		AND sd.droit & '.STR_MODIFIER.'
	) OR structure.id = '.$MEMBRE->id_structure.' ) ';
}

$str = new reqa('SELECT secuhtml::nom, absint::id FROM structure WHERE actif=1 '.$where.' ORDER BY nom '); 

// Liste de lieu 

if( in_array( $VT['ville'], array(VISUEL_VILLE_UNE, VISUEL_VILLE_LISTE) ) )
{
	$ville = new reqa('SELECT Lieu_id id, Lieu_ville ville FROM Lieu ORDER BY Lieu_Ville '); 
	$grp_lieu = new lieu_grp_ls( array('mode' => reqo::NORMAL ) ); 
	$grp_lieu->requete(); 
}

if( $VT['contact'] == TRUE ) 
{
	ajt_script('ajax.js') ;
	ajt_script('visuel.js') ;

	if( $aff->id_structure )
	{
		$ls_contact = new liste_contact; 
		$ls_contact->fi_contact_structure = $aff->id_structure; 
		$ls_contact = $ls_contact->requete(); 
	}
}

include HAUT_ADMIN;
?>

<h1>Visuel : <?php echo $VT['nom'] ?></h1>

<p><a href="visuel.php?t=<?php echo $t ?>" >Retour</a></p>

<?php pmess() ?>


<?php if(!$modif) : ?>
<form action="visuel-form.php" method="post" enctype="multipart/form-data" >

<table>

	<?php if( $VT['contenu'] == TRUE ): ?>
	<tr>	
		<td width="30%">Titre : </td>
	  <td><input name="titre" type="text" value="<?php echo $aff->titre ?>" size="40" /></td>
	</tr>
	<tr>
		<td>Description : </td>
		<td><textarea name="texte" cols="70" rows="7" ><?php echo $aff->texte ?></textarea></td>
	</tr>
	<?php endif ?>

	<?php if( $VT['date'] == TRUE ) : ?>
	<tr>
		<td>Date de début de la diffusion de l'affichette sur l'agenda : </td>
		<td><input type="text" name="datedeb" id="DateDeb" value="<?php echo $aff->datedeb ?>" />
			<img src="<?php echo RETOUR ?>jscalendar/img.gif" id="DateDeb_trigger" />
			<script type="text/javascript">
			    Calendar.setup({
				inputField     :    "DateDeb",     // id of the input field
				ifFormat       :    "%Y-%m-%d",      // format of the input field
				button         :    "DateDeb_trigger",  // trigger for the calendar (button ID)
				align          :    "Tl",           // alignment (defaults to "Bl")
				singleClick    :    true
			    });
			</script>
		</td>
	</tr>
	<tr>
		<td>Date de fin de la diffusion de l'affichette sur l'agenda : </td>
		<td><input type="text" name="datefin" id="DateFin" value="<?php echo $aff->datefin ?>" />
			<img src="<?php echo RETOUR ?>jscalendar/img.gif" id="DateFin_trigger" />
			<script type="text/javascript" >
				Calendar.setup({
				inputField     :    "DateFin",     // id of the input field
				ifFormat       :    "%Y-%m-%d",      // format of the input field
				button         :    "DateFin_trigger",  // trigger for the calendar (button ID)
				align          :    "Tl",           // alignment (defaults to "Bl")
				singleClick    :    true
			    });
			</script>
		</td>
	</tr>
	<?php endif ?>
	<tr>
	<td>Image (largeur : <?php echo $VT['img']['large'] ?> pixels, 
		hauteur : <?php echo $VT['img']['haut'] ?> pixels, format jpg, si votre image est plus grande elle sera recadrée(rapport format A4)) : </td>
	<td><input type="file" name="img" /><br />
	<input type="hidden" name="max_file_size" value="999999" />
		<?php if(!empty($aff->img) ) : ?>
			<img src="<?php echo C_DOS_PHP.$VT['dos'].$aff->img ?>" />
		<?php endif ?>
	</td>
	</tr>
	<tr>
	<td>Adresse Internet quand on clique sur le visuel (avec "http://") : </td>
	<td><input type="text" name="url" value="<?php echo $aff->url ?>" /></td>
	</tr>

	<?php if( $VT['structure'] == TRUE ) : ?>
	<tr>
	<td>Structure diffuseur : </td> 
	<td><select name="ids" id="ids" onchange="maj_contact()" >
		<?php while($s = $str->parcours() ) : ?>
			<option value="<?php echo $s->id ?>" 
				<?php if($s->id == $aff->acc_id_structure() ) : ?>selected="selected"<?php endif ?> >
				<?php echo $s->nom ?>
			</option>
		<?php endwhile ?>
	</select>
	</td>
	</tr>
		<?php if( $VT['contact'] == TRUE ) : ?>
		<tr>
			<td>Contact (informations affichées dans le début du descriptif dessous le visuel) : </td>
			<td><select id="md_contact" name="idc" >
			<?php if( $aff->id_structure != 0 ) : ?>
				<option value="0" >Sans contact</option>
				<?php while( $c = $ls_contact->parcours() ) : ?>
					<option value="<?php echo $c->id ?>" <?php selected($c->id == $aff->id_contact) ?> >
						<?php echo $c->titre, ' ', $c->tel, ' [', $c->site, ']' ?>
					</option>
				<?php endwhile ?>
			<?php endif ?>
			</select>
			</td>
		</tr>
		<?php endif ?>
	<?php endif ?>

	<?php if( $VT['ville'] == VISUEL_VILLE_UNE ) : ?>
		<tr>
			<td>Commune (page commune dans l'agenda où sera affiché le visuel) :</td> 
			<td>
			<select name="ville" >
			<?php while($v = $ville->parcours() ) : ?>
				<option value="<?php echo $v->id ?>" <?php if( $v->id == $aff->ville ) : ?>selected="selected"<?php endif ?> >
				<?php echo $v->ville ?></option>
			<?php endwhile ?>
			</select>
			</td>
		</tr>
	<?php elseif( $VT['ville'] == VISUEL_VILLE_LISTE) : ?>
		<tr>
			<td>Groupe de lieux :</td> 
			<td>
			<select name="grpl[]" multiple="yes" size="8" >
			<?php while($gl = $grp_lieu->parcours() ) : ?>
				<option value="<?php echo $gl->acc_id() ?>" 
					<?php if( in_array($gl->acc_id(), $aff->tab_grp_lieu) ) : ?>selected="selected"<?php endif ?> >
				<?php echo $gl->acc_nom() ?></option>
			<?php endwhile ?>
			</select>
			</td>
		</tr>
		<tr>
			<td>Commune (s) :</td> 
			<td>
			<select name="ville[]" multiple="yes" size="8" >
			<?php while($v = $ville->parcours() ) : ?>
				<option value="<?php echo $v->id ?>" <?php if( in_array($v->id, $aff->tab_ville) ) : ?>selected="selected"<?php endif ?>>
				<?php echo $v->ville ?></option>
			<?php endwhile ?>
			</select>
			</td>
		</tr>
	<?php endif ?>
</table>

	<p><input type="submit" name="ok" value="Ok !" />
	<input type="hidden" name="id" value="<?php echo $aff->id ?>" />
	<input type="hidden" name="t" value="<?php echo $t ?>" />
	</p>
</form>

	<?php if( empty($aff->id_structure ) ) : ?>
		<script type="text/javascript" >
		maj_contact(); 
		</script>
	<?php endif ?>

<?php else : ?>
	<p>La demande a été prise en compte.</p>
<?php endif ?>
<?php include BAS_ADMIN ?>
