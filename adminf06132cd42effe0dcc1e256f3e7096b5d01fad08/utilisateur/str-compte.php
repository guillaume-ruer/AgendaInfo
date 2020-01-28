<?php
require '../../include/init.php'; 
require C_INC.'structure_class.php'; 

$num_page= isset($_GET['p']) ? abs((int)$_GET['p']) : 1; 

$dos = RETOUR.'JSCal2/';
$PAT->ajt_style('css/jscal2.css', $dos);
$PAT->ajt_style('css/border-radius.css', $dos);
$PAT->ajt_style('str-compte.css', C_ADMIN.'utilisateur/style/');

$PAT->ajt_script('js/jscal2.js', $dos );  
$PAT->ajt_script('js/lang/fr.js', $dos );  

// Autre script 
$PAT->ajt_script('jquery-3.1.1.min.js');
$PAT->ajt_script('str-compte.php', C_ADMIN.'utilisateur/javascript/');

$where = ''; 
$nom = ''; 

if(!empty($_GET['nom']) )
{
	$crit = "'%".secubdd($_GET['nom'])."%'"; 
	$where .= ' AND (nom LIKE('.$crit.') OR conv LIKE('.$crit.') )'; 
	$nom = secuhtml($_GET['nom']); 
}

$type = -1; 

if( isset($_GET['type']) && isset(structure::$tab_type[ $_GET['type'] ]) )
{
	$type = $_GET['type']; 
}

$str_payant = (bool)($_GET['payant'] ?? !isset($_GET['ok']) ); 

if( $str_payant )
{
	$where .= ' AND payant=1 '; 
}

if( $type != -1 )
{
	$where .= ' AND type=\''.$type.'\' '; 
}

$sql = '
SELECT 
	id, numero, nom, actif, date_fin_adhesion, 
	type, email, conv, payant, rappel, rappel_facture
FROM structure 
WHERE 1 
'.$where; 


$donne = req('SELECT COUNT(*) tot FROM (
		'.$sql.'
	) tmp 
'); 

$do = fetch($donne); 

$nb_par_page = 20; 
$total = $do['tot']; 
$nb_page = ceil($total/$nb_par_page); 

$from = ($num_page-1)*$nb_par_page;
$to = $nb_par_page; 

$donne = req($sql.'
	ORDER BY actif DESC, date_fin_adhesion
	LIMIT '.$from.', '.$to.'
');

$bt_reinit = !empty($nom) || $type!=-1; 
$url_pagin = 'str-compte.php?p=%pg&nom='.$nom.'&type='.$type.'&payant='.(int)$str_payant; 

$tab_rappel = [
	0 => '',
	1 => 'Rappel envoyé',
	2 => 'Adhésion + facture envoyé',
	3 => 'Relance + facture envoyé',
	4 => 'Relance + facture + annonce désactivation',
	5 => 'Désactivation'
];

require PATRON; 
