<?php
require_once '../../../include/init.php'; 
require_once C_INC.'structure_facture_class.php'; 
require_once C_INC.'structure_facture_fonc.php'; 
require_once C_INC.'fonc_memor.php'; 

if( !isset($_POST['id'], $_POST['somme'], $_POST['type']) )
{
	exit('fail - donnée manquantes'); 
}

$fichier = ''; 
$dosf = ''; 

if( isset($_FILES['file']['error']) && ($_FILES['file']['error'] == UPLOAD_ERR_OK) )
{
	require_once 'transfert_form_class.php';
	$dosf = structure_facture_dossier(); 

	$fichier = transfert_form::renom($_FILES['file']['name']); 
	move_uploaded_file($_FILES['file']['tmp_name'], $dosf.'/'.$fichier); 
	$dosf = str_replace('../', '', $dosf); 
}


if( isset($_POST['date']) )
{
	list($j, $m, $a) = explode('/', $_POST['date']);
	$time = mktime(0,0,0,$m, $j, $a);
}
else
{
	$time = time(); 
}

$sf = new structure_facture([ 
	'id' => $_POST['idf'], 
	'structure' => ['id' => $_POST['id'] ], 
	'somme' => str_replace(',', '.', $_POST['somme']), 
	'type' => $_POST['type'], 
	'date' => $time, 
	'fichier' => $fichier, 
	'dossier' => $dosf, 
]);

crud_enr($sf); 

$donne = req('SELECT id, structure structure__id, somme, type, date, fichier, dossier FROM structure_facture WHERE id='.(int)$sf->acc_id().' LIMIT 1'); 

if( $do = fetch($donne) )
{
	$sfa = new structure_facture($do); 
	$sfa->aff_ligne(); 
}
