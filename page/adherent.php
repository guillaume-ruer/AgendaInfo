<?php 
require_once '../include/init.php'; 
require_once C_INC.'departement_class.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'structure_class.php'; 
require_once C_INC.'structure_fonc.php'; 
require_once C_INC.'visuel_liste_class.php'; 
require_once C_INC.'visuel_fonc.php'; 
require_once C_INC.'adresse_class.php'; 
require_once C_ADMIN.'include/visuel_conf.php'; 
require_once C_INC.'ls_evenement_class.php'; 
require_once C_INC.'evenement_class.php'; 
require_once C_INC.'ville_class.php'; 
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'categorie_class.php'; 
require_once C_INC.'tarif_class.php'; 

visuel_const($VISUEL_CONF); 

define('NB_JOUR_VISIBLE', 180 ); 

//Récupération get 
$nbparpage = 20; 
$id_structure = ( isset( $_GET['id'] ) ) ? (int)$_GET['id'] : 0 ; 
$page = ( isset( $_GET['pg'] ) ) ? (int)$_GET['pg'] : 0; 
$theme = 0;

if( empty($id_structure) || ( ($str = str_init($id_structure) ) === FALSE ) )
{
	senva(); 
}

if($str->acc_actif() == structure::INACTIF )
{
	senva(); 
}

/*
	Traitement sur les dates 
*/

$input_date = date('Y-m-d'); 
$realdate = $datepast = ''; 
mes_date($input_date, NB_JOUR_VISIBLE, $realdate, $datepast);


//Evenements
$lsevent = new ls_evenement( array(
	'champ' => EVCH_DATE|EVCH_CAT|EVCH_LIEU|EVCH_DESC|EVCH_NB_DATE|EVCH_CONTACT|EVCH_TARIF, 
	'fi_date_min' => $realdate,
	'fi_date_max' => $datepast,
	'fi_structure' => $str->acc_id(), 
) ); 

$lsevent->acc_pagin()->mut_url(url_adherent($str->acc_id(), NULL, ID_LANGUE, $str->acc_nom() ) ); 
$lsevent->acc_pagin()->mut_num_page($page); 
$lsevent->mut_nb_par_page($nbparpage); 
$lsevent->requete(); 

// Google map  
$ville= $str->acc_adresse()->acc_ville()->acc_nom(); 
$rue = $str->acc_adresse()->acc_rue(); 

if(empty($ville) && empty($rue)  )
{
	$taille_google = 7; 
	$google = 'Limousin, france'; 
	$addresse = ''; 
}
else
{
	$taille_google = 16; 
	$google = 'Limousin, '.$ville.', '.$rue ;
	$addresse = $rue.' - '.$str->acc_adresse()->acc_ville()->acc_cp().' '.$ville; 
}

$nom_theme = ' tous les thèmes ';
$datedeb = date_format_fr($realdate); 
$nom_lieu = 'Tout le limousin'; 
$info_filtrage = $nom_lieu . ' - à partir du ' . $datedeb . ' - ' . $nom_theme ;  

$date_affiche = date_format_fr(date('Y-m-d') ); 
$url_drapeau_fr = ADD_SITE.'';
$url_drapeau_en = ADD_SITE.'';

// Pub et expo 

$PAT->ajt_style('style_ville.css'); 
//APIKEY viens du fichier init_module présent dans le dossier include du module (page/include/)
// $PAT->ajt_script('https://maps.googleapis.com/maps/api/js?key='.$APIKEY.'&callback=initMap', NULL); 

$PAT->mut_titre(NOM_SITE.' :: '.$str->acc_nom() ); 
$PAT->def_patron(); 
$PAT->ajt_patron('bandeau.p.php'); 
$PAT->ajt_patron('adherent.p.php'); 
$PAT->ajt_patron('pied-page.php'); 

$logo = $str->acc_logo(); 
$facebook = $str->acc_facebook(); 

include PATRON; 
memor_url(); 
