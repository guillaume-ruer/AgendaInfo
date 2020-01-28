<?php
require '../include/init.php'; 
require_once C_INC.'ls_evenement_class.php'; 
require_once C_INC.'evenement_fonc.php'; 
require_once C_INC.'affichette_fonc.php'; 

http_param( array('ida' => 0, 'p' => 0) ); 

$event = empty($ida) ? FALSE : event_init( $ida ); 

$affichette = new ls_evenement( array(
	'fi_type' => evenement::AFFICHE, 
	'champ' => EVCH_AFFICHE|EVCH_DESC|EVCH_CONTACT|EVCH_DATE,
	'nb_par_page' => 10, 
	'num_page' => $p, 
	'fi_date_max' => NULL, 
	'fi_str_actif' => FALSE,
	'fi_date_max' => NULL 
) );

$affichette->mut_pagin( array( 
	'url' => 'affichettes-%pg.html', 
	'num_page' => $p,
) ); 

$affichette->requete(); 

$PAT->ajt_style('style_affichette.css'); 
$PAT->def_patron();
$PAT->ajt_patron('bandeau.p.php'); 
$PAT->ajt_patron('affichette_p.php', 'patron/' ); 
$PAT->ajt_patron('pied-page.php'); 

require PATRON; 
