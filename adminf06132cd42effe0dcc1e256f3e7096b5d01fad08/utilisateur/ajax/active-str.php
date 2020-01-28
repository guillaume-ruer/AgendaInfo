<?php 
include '../../../include/init.php'; 

if( isset($_GET['id'], $_GET['etat']) && isset(structure::$tab_etat[ $_GET['etat'] ] ) )
{
	$donne = prereq('SELECT actif FROM structure WHERE id=?'); 
	exereq($donne, [ $_GET['id'] ] ); 

	if( $do = fetch($donne) )
	{
		echo structure::$tab_class_etat[$_GET['etat'] ]; 
		req('UPDATE structure SET actif='.(int)$_GET['etat'].' WHERE id='.(int)$_GET['id']); 
	}
	else
	{
		echo 'inexistant'; 
	}
}
else
{
	echo 'fail'; 
}
