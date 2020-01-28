<?php 
require_once '../include/init.php'; 
require_once C_INC.'departement_class.php'; 
require_once C_INC.'evenement_class.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'structure_class.php'; 
require_once C_INC.'ls_evenement_class.php'; 
require_once C_INC.'adresse_class.php'; 
require_once C_INC.'ville_class.php'; 
require_once C_INC.'ville_fonc.php'; 
require_once C_INC.'reqa_class.php';
require_once C_INC.'fonc_bandeau.php'; 
require_once C_INC.'visuel_liste_class.php'; 
require_once C_INC.'visuel_fonc.php'; 
require_once C_INC.'categorie_class.php'; 
require_once C_INC.'tarif_class.php'; 
require_once C_ADMIN.'include/visuel_conf.php'; 
require_once C_INC.'lien_ls.php'; 
require_once C_INC.'lien_grp_ls.php'; 
require_once C_INC.'lien_class.php'; 

define('NB_JOUR_VISIBLE', 180 ); 

visuel_const($VISUEL_CONF); 

$nbparpage = 20; 
//Récupération get 
$id_lieu = ( isset( $_GET['id'] ) ) ? (int)$_GET['id'] : 0 ; 
$page = ( isset( $_GET['pg'] ) ) ? (int)$_GET['pg'] : 0; 

if( empty($id_lieu) || ($ville = ville_init($id_lieu) ) === FALSE )
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
//Evenements
$lsevent = new ls_evenement( array(
	'champ' => EVCH_DATE|EVCH_CAT|EVCH_LIEU|EVCH_DESC|EVCH_NB_DATE|EVCH_CONTACT|EVCH_TARIF, 
	'fi_date_min' => $realdate,
	'fi_date_max' => $datepast,
	'fi_lieu' => $ville->acc_id(),
) ); 

$lsevent->acc_pagin()->mut_url( url_ville($ville->acc_id(), NULL, ID_LANGUE, $ville->acc_nom() ) ); 
$lsevent->acc_pagin()->mut_num_page($page); 
$lsevent->mut_nb_par_page($nbparpage); 
$lsevent->requete(); 

// Liens 
/*
$tab_lien=array(); 
$ls_lien_grp = new lien_grp_ls;
$ls_lien_grp->mut_non_vide(TRUE); 
$ls_lien_grp->mut_fi_lieu($ville->acc_id() ); 
$ls_lien_grp->requete(); 

while( $lg = $ls_lien_grp->parcours() )
{
	$r = new lien_ls( array('type'=>$lg->acc_id(), 'lieu' => $ville->acc_id(), 'limite' => 20 ) ); 
	$r->requete(); 
	$tab_lien[ ] = array('lg' => $lg, 'liste' => $r ); 
}
*/

$nom_theme = ' Tous les thèmes ';
$datedeb = date_format_fr($realdate); 
$info_filtrage = $ville->acc_nom() . ' - A partir du ' . $datedeb . ' - ' . $nom_theme ;  
$titre = 'Info Limousin :: '.$ville->acc_nom().' ('.$ville->acc_dep()->acc_num().')' ; 

$date_affiche = date_format_fr( date('Y-m-d') ); 
$url_drapeau_fr = ADD_SITE.'';
$url_drapeau_en = ADD_SITE.'';

//Crée les tableau à partir de la table bandeaux 
// $pub = recupe_bandeau('pub', 1); 

//  pub, expo des adhérents 
/*
$opub = new visuel_liste($VISUEL_CONF, PUB); 
$opub->nb=1; 
$opub->ville=absint($ville->acc_id() ); 
$opub->ordre = visuel_liste::O_ALEAT; 
$pub = $opub->requete(); 

$oexpo = new visuel_liste($VISUEL_CONF, EXPO ); 
$oexpo->nb=1; 
$oexpo->ville=absint($ville->acc_id() ); 
$oexpo->ordre = visuel_liste::O_ALEAT; 
$expo = $oexpo->requete(); 
*/

$PAT->mut_titre($titre); 

//APIKEY viens du fichier init_module présent dans le dossier include du module (page/include/)
$PAT->ajt_script('http://www.google.com/jsapi?key='.$APIKEY, NULL); 
$PAT->ajt_script('jquery.js'); 
$PAT->ajt_script('lien_deroulant.js'); 
$PAT->ajt_style('style_ville.css'); 
$PAT->def_patron(); 
$PAT->ajt_patron('bandeau.p.php'); 
$PAT->ajt_patron('ville.p.php'); 
$PAT->ajt_patron('pied-page.php');

include PATRON; 
memor_url(); 
