<?php
require '../include/init.php'; 

$PAT->ajt_style('inscription.css', RETOUR.'membre/style/'); 

$valide = FALSE; 

if( isset($_GET['m'], $_GET['c']) )
{
	$pre = prereq('SELECT id FROM Utilisateurs WHERE User=? AND code_conf=?'); 
	exereq($pre, [ $_GET['m'], $_GET['c'] ]); 

	if( fetch($pre) )
	{
		$valide = TRUE; 
		$pre = prereq('UPDATE Utilisateurs SET code_conf=\'\' WHERE User=?'); 
		exereq($pre, [ $_GET['m'] ]);
	}
}

require PATRON; 
