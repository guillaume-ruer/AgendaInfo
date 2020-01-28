<?php
include '../include/init.php'; 
include C_INC.'structure_class.php'; 
require_once C_INC.'evenement_fonc.php'; 
define('NB_MOIS', 4); 


//Mettre un de moins que le nombre réel voulu. 
$nb_date_col = 20;
$nb_col = 4;

if( !isset($_GET['id']) || !($ev = event_init($_GET['id']) ) )
{
	senva(); 
}

/*
	Vérification si la structure gérant l'événement est actif 

*/
if( $ev->acc_contact()->acc_structure()->acc_actif() != structure::ACTIF )
{
	senva(); 
}

/*
	Vérification de l'état de l'évenement 
*/

if( $ev->acc_etat() != evenement::ACTIF )
{
	senva(); 
}


/*
	Calcule pour l'affichage des dates en colonnes 
*/
$nb_date = count( $ev->acc_tab_date() ); 

if($nb_date > $nb_col*$nb_date_col )
{
	$nb_date_col = floor( $nb_date / $nb_col) ; 
}

$la_date = ( isset($tab_date[0]) ) ? 'Le '.$tab_date[0] : 'Evénement passé'; 
$i=-1; 
$tab_date = array(
	'Janvier', 'Février', 'Mars', 
	'Avril', 'Mai', 'Juin', 
	'Juillet', 'Août', 'Septembre', 
	'Octobre', 'Novembre', 'Décembre' 
);

$tab_jour = array( 'Dim','Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam' ); 

$PAT->def_patron(); 
$PAT->mut_titre(NOM_SITE.' :: '.secuhtml($ev->acc_titre()) ); 
$PAT->ajt_patron('bandeau.p.php'); 
$PAT->ajt_patron('autre-date.p.php'); 
$PAT->ajt_patron('pied-page.php'); 
$PAT->ajt_style('style_page.css'); 
include PATRON; 
memor_url(); 
