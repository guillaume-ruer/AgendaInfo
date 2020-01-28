<?php
require '../../include/init.php'; 
require_once C_INC.'structure_class.php'; 

header('content-type: application/json, charset=utf8'); 

http_param( array('nom'=>'') ); 
$nom = trim($nom); 
if( strlen($nom) < 1 )
	exit(); 

$nom='%'.$nom.'%'; 
$tab= array(); 

/*
	Ville 
*/
$donne = req('SELECT id, nom FROM structure WHERE nom LIKE( \''.secubdd($nom).'\' ) LIMIT 50'); 

while( $do = fetch($donne) ) 
{
	$u = new structure(genere_init($do) ); 
	$tab[] = array(
		'id' => $u->acc_id(), 
		'proposition' => $u->proposition(),
		'etiquette' => $u->etiquette(),
		'json' => $u->json(), 
	); 
}

echo json_encode($tab); 
