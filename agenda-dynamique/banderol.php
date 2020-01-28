<?php
require '../include/init.php';
require C_INC.'visuel_fonc.php'; 

if( isset($_GET['id']) )
{
	$donne = req('SELECT id, URL url FROM Bandeaux WHERE id='.(int)$_GET['id']);

	if( $do = fetch($donne) )
	{
		req('UPDATE Bandeaux SET Clics=Clics+1 WHERE id='.(int)$do['id']);
		header('Location: '.$do['url']);
	}
}

exit(); 
