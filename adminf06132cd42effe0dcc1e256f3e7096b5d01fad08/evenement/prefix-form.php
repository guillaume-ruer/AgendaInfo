<?php
include '../../include/init.php'; 

non_autorise(PREFIX); 

http_param(array( 'idp' => 0 ) );

if(isset($_POST['ok']) )
{
	$pre = trim($_POST['prefixe']); 

	if(!empty($pre) )
	{
		if( !empty($idp ) )
		{
			req('UPDATE prefixe_event SET prefixe=\''.secubdd($pre).'\' WHERE id=\''.$idp.'\' LIMIT 1  ');
			mess('Préfixe modifier : '.secuhtml($pre) );
		}
		else
		{
			req('INSERT INTO prefixe_event(prefixe) VALUES(\''.secubdd($pre).'\') '); 
			mess('Préfixe ajouté : '.secuhtml($pre) );
		}
	}
	else
	{
		mess('Préfixe vide.'); 
	}
}

$prefixe = ''; 

$donne = req('SELECT id, prefixe FROM prefixe_event WHERE id='.$idp.' LIMIT 1 '); 

if($do = fetch($donne ) )
{
	$prefixe = secuhtml($do['prefixe']);
	$idp = (int)$do['id'];
}

include PATRON; 
