<?php
require_once '../include/init.php'; 
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'visuel_class.php'; 
require_once C_INC.'visuel_liste_class.php'; 
require_once C_INC.'visuel_fonc.php'; 
require_once C_ADMIN.'include/visuel_conf.php'; 

visuel_const($VISUEL_CONF); 

http_param(array( 'p' => 0, 'ida' => 0, 't' => EXPO ) ); 

$laffichette=FALSE;
$PAT->mut_titre(NOM_SITE.' :: Visuels'); 

if( !empty($ida) )
{
	$v = new visuel($VISUEL_CONF);
	$v->init($ida); 
	$laffichette =TRUE; 
	$PAT->mut_titre(NOM_SITE.' :: '.$v->titre ); 
}

$affichette = new visuel_liste($VISUEL_CONF, $t ); 
$affichette->page = $p; 
$affichette->pagin=TRUE;
$affichette->ordre= in_array($t, array(IPNS, TELE) ) ? visuel_liste::O_ID : visuel_liste::O_DATE; 
$affichette->nb=10;
$affichette->fi_date=visuel_liste::D_ACTUEL; 
$affichette = $affichette->requete(); 

$i=0;
$nom = strtolower($VISUEL_CONF[ $t ]['nom']);
$page = $VISUEL_CONF[ $t ]['page']; 

$PAT->ajt_style('style_affichette.css'); 
$PAT->def_patron();
$PAT->ajt_patron('bandeau.p.php'); 
$PAT->ajt_patron('visuel.p.php'); 
$PAT->ajt_patron('pied-page.php'); 
include PATRON;
memor_url(); 
