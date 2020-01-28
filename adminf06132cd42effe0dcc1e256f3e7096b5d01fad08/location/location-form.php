<?php 
include '../../include/init.php'; 
include C_INC.'location_class.php'; 
include C_INC.'structure_fonc.php'; 
include C_INC.'reqa_class.php'; 
include C_INC.'ls_contact_class.php'; 

/*
	Init
*/ 

// Droit : GERER_UTILISATEUR | droite de modification sur la structure 
http_param(array('idl' => 0, 'ids' => 0 ) ); 

$loc = new location; 

if( $loc->init($idl) )
{
	if( !droit(GERER_UTILISATEUR)  )
	{
		if(!str_droit_utilisateur($loc->id_structure ) )
		{
			page_erreur(); 
		}
	}
}

/*
	Traitement 
*/

$ch_lieu = new barre_proposition_form(['fichier'=> 'lieu.php', 'class' => 'ville', 'label' => 'Communes '] ); 

$formulaire = TRUE; 

if( isset($_POST['ok']) )
{


	http_param( array('titre_flux' => FALSE, 'contact' => array(), 
		'nom'=>'', 'style' => '', 'grplieu' => array(), 'theme' => array(), 'str' => array() ) ); 
	
	$lieu = []; 

	$tmp_lieu = $ch_lieu->donne(); 

	foreach($tmp_lieu as $l )
	{
		$lieu[] = $l->id(); 
	}

	$loc->nom = $nom; 
	$loc->css = $style; 
	$loc->lslieux = $lieu; 
	$loc->lscontact = $contact; 
	$loc->lsgrplieux = $grplieu; 
	$loc->lstheme = $theme; 
	$loc->lsstr = $str; 
	$loc->id_structure = $ids;
	$loc->titre_flux = (bool)$titre_flux; 
	$loc->enr(); 
	mess('Les modifications ont été enregistrées avec succès.'); 
	$formulaire = FALSE; 
}

/*
	Affichage 
*/

if( $formulaire )
{
	//Lieu : 

/*
	$requete = 'SELECT Lieu_Ville as ville, Lieu_ID AS id FROM Lieu WHERE Lieu_Dep = %dep ORDER BY ville';  
	$regle = array('id'=>'absint', 'ville' => 'secuhtml') ; 
	$correze = new reqa(str_replace('%dep', 19, $requete), $regle ); 
	$haute_vienne = new reqa(str_replace('%dep', 87, $requete), $regle ); 
	$creuze = new reqa(str_replace('%dep', 23, $requete), $regle ); 
*/

	// Groupe de lieu 
	$grplieu = new reqa('SELECT absint::id, secuhtml::Nom nom FROM Lieu_grp WHERE Nom!=\'\' ORDER BY ordre, Nom'); 

	// Contact 
	$contact = new liste_contact; 
	$cont = $contact->requete(); 

	// Structure 
	$structure = new reqa('SELECT absint::id, secuhtml::nom FROM structure ORDER BY nom '); 

	// Thème 
	$theme = new reqa('SELECT absint::id, secuhtml::nom_fr nom FROM categories_grp ORDER BY nom_fr'); 

	$ch_lieu->mut_donne($loc->tab_ob_lieu); 
}


http_param( array('page' => 0 , 'p' => 0 ) ); 
$tabp = array( 'location.php', 'location-admin.php' ); 
$url = isset($tabp[ $page ]) ? $tabp[ $page ] : $tabp[ 0]; 
$url .= '?ids='.$ids.'&amp;p='.$p; 

include HAUT_ADMIN; 
?>

<h1>Modifier un relais</h1>

<?php pmess() ?>

<p><a href="<?php echo $url ?>" >Retour</a></p>

<?php if( $formulaire ) : ?>

<form action="location-form.php" method="post" >

<p>Nom : <input type="text" name="nom" value="<?php echo $loc->nom ?>" />
	(<label>Utiliser ce nom dans le titre du flux : <input type="checkbox" name="titre_flux" <?php checked($loc->titre_flux) ?> /></label>)
</p>

<p>Style : <select name="style" >
	<option value="" >Choisir</option>
	<?php 
	$dos = opendir(RETOUR.'style'); 
	while( FALSE !== ( $fichier = readdir($dos) ) ) : ?>
		<?php if( preg_match('`^style_(.+)\.css`', $fichier, $match ) ) : ?>
		<option value="<?php echo $match[1] ?>" <?php selected($match[1] == $loc->css ) ?> ><?php echo $match[1]  ?></option>	
		<?php endif ?>
	<?php 
	endwhile;
	closedir($dos); 	
	?>
</select>
</p> 

<p class="info_event" >Pour sélectionner plusieurs champs, cliquez en appuyant sur la touche 'Commande' (Mac) ou 'Ctrl' (PC).</p>

<p class="info_event" >Notez bien : par défaut, si aucun champ d'un filtre n'est sélectionné, il n'y a pas de filtrage.<br /> 
Par exemple, si vous souhaitez afficher les événements de l'ensemble du Limousin, ne sélectionnez aucun champ dans "groupe de lieux" et "lieux".
</p>

<p class="info_event" >Le filtrage se fait par un "et" entre les filtres et un "ou" entre les options du même filtre.</p>

<fieldset >
	<legend >Groupe de lieux</legend>
	
	<p><select name="grplieu[]" multiple="multiple" size="12" >
		<?php while( $g = $grplieu->parcours() ) : ?>
			<option value="<?php echo $g->id ?>" <?php selected(in_array($g->id, $loc->lsgrplieux ) ) ?> >
			<?php echo $g->nom ?>
			</option>
		<?php endwhile ?>
	</select>
	</p>
</fieldset>

<fieldset>
        <legend>Lieu(x)</legend>

		<?php $ch_lieu->aff() ?>

<?php /*
        <div id="colonne_lieu" >
                <div>
                        <p class="calign" >Corrèze</p>
                        <select multiple="multiple" name="lieu[]" size="12" >
                                <?php while($cor = $correze->parcours() ) : ?>
                                <option value="<?php echo $cor->id; ?>" <?php selected(in_array($cor->id, $loc->lslieux ) ) ?> ><?php echo $cor->ville; ?>
				</option>
                                <?php endwhile; ?>
                        </select>
                </div>
                <div>
                        <p class="calign" >Creuse</p>
                        <select multiple="multiple" name="lieu[]" size="12" >
                                <?php while( $cr = $creuze->parcours() ) : ?>
                                <option value="<?php echo $cr->id; ?>" <?php selected(in_array($cr->id, $loc->lslieux ) ) ?> > <?php echo $cr->ville; ?>
				</option>
                                <?php endwhile; ?>
                        </select>
                </div>
                <div>
                        <p class="calign" >Haute-Vienne</p>
                        <select multiple="multiple" name="lieu[]" size="12" >
                                <?php while($hv = $haute_vienne->parcours() ) : ?>
                                <option value="<?php echo $hv->id; ?>" <?php selected(in_array($hv->id, $loc->lslieux ) ) ?> > <?php echo $hv->ville; ?>
				</option>
                                <?php endwhile; ?>
                        </select>
                </div>
        </div>

        <div class="stop_float" ></div>
		*/ ?>
</fieldset>

<fieldset >
	<legend >Structure</legend>
	
	<p>Accepte les événements de tous les contacts des structures séléctionnées.</p>

	<p><select name="str[]" multiple="multiple" size="12" >
		<?php while( $s = $structure->parcours() ) : ?>
			<option value="<?php echo $s->id ?>" <?php selected( in_array($s->id, $loc->lsstr) ) ?> >
			<?php echo $s->nom ?>
			</option>
		<?php endwhile ?>
	</select>
	</p>
</fieldset>

<fieldset >
	<legend >Contact</legend>
	
	<p><select name="contact[]" multiple="multiple" size="12" >
		<?php while( $c = $cont->parcours() ) : ?>
			<option value="<?php echo $c->id ?>" <?php selected(in_array($c->id, $loc->lscontact) ) ?> >
			<?php echo $c->nom, ', ',$c->titre,' (', $c->ville, ')' ?>
			</option>
		<?php endwhile ?>
	</select>
	</p>
</fieldset>

<fieldset >
	<legend >Thème</legend>
	
	<p><select name="theme[]" multiple="multiple" size="10" >
		<?php while( $t = $theme->parcours() ) : ?>
			<option value="<?php echo $t->id ?>" <?php selected( in_array($t->id, $loc->lstheme) ) ?> >
			<?php echo $t->nom ?>
			</option>
		<?php endwhile ?>
	</select>
	</p>
</fieldset>

<p><input type="hidden" name="ids" value="<?php echo $ids ?>" />
<input type="hidden" name="idl" value="<?php echo $loc->id ?>" />
<input type="hidden" name="p" value="<?php echo $p ?>" />
<input type="hidden" name="page" value="<?php echo $page ?>" />
<input type="submit" name="ok" value="Ok !" />
</p>

</form>
<?php endif ?>

<?php include BAS_ADMIN ?>
