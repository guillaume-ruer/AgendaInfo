<?php
require '../../include/init.php'; 

define('GS_NOUVEAU', 0); 
define('GS_MODIF', 1); 

$mode = GS_NOUVEAU; 

if( isset($_GET['ids']) )
{
	req('DELETE FROM structure_grp WHERE id='.(int)$_GET['ids'].' LIMIT 1'); 
}
elseif( isset($_POST['ok']) )
{
	if( isset($_POST['idm']) )
	{
		req('UPDATE structure_grp SET nom=\''.secubdd($_POST['nom']).'\' WHERE id='.(int)$_POST['idm'].' LIMIT 1 '); 
	}
	else
	{
		req('INSERT INTO structure_grp(nom) VALUES(\''.secubdd($_POST['nom']).'\')'); 
	}
}
elseif( isset($_GET['idm']) )
{
	$donne = req('SELECT * FROM structure_grp WHERE id='.(int)$_GET['idm'].' LIMIT 1 '); 
	if( $do = fetch($donne) )
	{
		$groupe = $do; 
		$mode = GS_MODIF; 
	}
}

$lsg = req('SELECT id, nom FROM structure_grp'); 

require PATRON; 
