<?php
require '../include/init.php';
require_once C_INC.'ls_evenement_class.php';
require_once C_INC.'evenement_class.php'; 
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'structure_class.php'; 
require_once C_INC.'structure_fonc.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'categorie_class.php'; 
require_once C_INC.'tarif_class.php'; 
require_once C_INC.'ville_class.php'; 
require_once C_INC.'article_class.php'; 
require_once C_INC.'article_fonc.php'; 

list( $y, $m, $d ) = explode('-', date('Y-m-d',time() - 3*24*3600 ) ); 
define('LS_EVENT_TIME_MIN',mktime(0,0,0,$m,$d,$y) ); 

$PAT->ajt_style('accueil.css'); 

$donne = req('
	SELECT COUNT( DISTINCT Evenement_id ) AS nb
	FROM Evenement_dates
	WHERE Evenement_dates.Evenement_Date >= NOW() 
'); 
$do = fetch($donne); 

$nb_event = (int)$do['nb'];

//Mes 10 dernier événement saisi 
$mes10der = new ls_evenement(array(
	'champ' => EVCH_ETAT|EVCH_DATE_CREATION|EVCH_CREATEUR,	
	'fi_actif' => ls_evenement::TOUT,
	'fi_str_actif' => false, 
	'fi_date' => FALSE, 
	'fi_createur' => $MEMBRE->id, 
	'order' => ls_evenement::ORDER_MES10DER, 
) ); 

//$mes10der->titre_racourci = TRUE;
$mes10der->requete(); 

//Mes 10 dernier événements modifier 
$mes10dermod = new ls_evenement(array(
	'champ' => EVCH_ETAT|EVCH_DER_DATE_MODIF|EVCH_DER_MODIFIEUR,
	'fi_actif' => ls_evenement::TOUT, 
	'fi_str_actif' => false, 
	'fi_date' => FALSE,
	'fi_modifieur' => $MEMBRE->id,  
	'order' => ls_evenement::ORDER_DER_MODIF, 
) ); 
//$mes10dermod->titre_racourci = TRUE;
$mes10dermod->requete(); 

//Les 20 derniers événements saisi 
$der20 = new ls_evenement( array(
	'champ' => EVCH_ETAT|EVCH_CREATEUR|EVCH_DATE_CREATION,
	'fi_actif' => ls_evenement::TOUT,
	'fi_date' => FALSE, 
	'fi_str_actif' => TRUE, 
	'nb_par_page' => 20, 
	'fi_date_creat_min' => LS_EVENT_TIME_MIN, 
	'order' => ls_evenement::ORDER_DATE_CREATION,  
) ); 

//$der20->titre_racourci = TRUE;
$der20->requete(); 

//Les 20 derniers événements modifié
$der20mod = new ls_evenement( array( 
	'champ' => EVCH_ETAT|EVCH_DER_MODIFIEUR|EVCH_DER_DATE_MODIF, 
	'fi_date' => FALSE, 
	'fi_actif' => ls_evenement::TOUT, 
	'fi_str_actif' => TRUE, 
	'fi_date_modif_min'=> LS_EVENT_TIME_MIN, 
	'nb_par_page' => 20,
	'order' => ls_evenement::ORDER_DER_MODIF, 
) ); 

//$der20mod->titre_racourci = TRUE;
$der20mod->requete(); 

/*
	Stucture du membre connecté 
*/

$str = str_init($MEMBRE->id_structure);

/*
	Stat 
*/

$donne = req('
	SELECT COUNT(*) nb FROM 
	(
		SELECT e.id  FROM Evenement e
		LEFT JOIN Evenement_dates ed
			ON ed.Evenement_id=e.id
		WHERE Creat_datetime BETWEEN \''.date('Y').'-01-01\' AND \''.date('Y').'-12-31\' 
		AND Creat_id='.absint($MEMBRE->id).'
		GROUP BY e.id 
	) t 
');

$do = fetch($donne); 
$nb_cree_membre = absint($do['nb']); 

$donne = req('
	SELECT COUNT( DISTINCT idevent ) nb 
	FROM historique 
	WHERE idutr='.absint($MEMBRE->id).' 
	AND type=2
	AND date BETWEEN '.mktime(0,0,0,1,1,date('Y') ).' AND '.mktime(0,0,0,12,31,date('Y') ).'
'); 
$do = fetch($donne); 
$nb_modif_membre = absint($do['nb']); 

require PATRON; 
