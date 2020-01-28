<?php
include '../../include/init.php'; 
include 'include/phrase_mail_fonc.php'; 

http_param(array('id' => 0, 'phrase' => '', 'dim' => '' ) ); 
$affiche = TRUE; 

if(isset($_POST['ok'] ) )
{
	$affiche = FALSE; 
	if(empty($id) )
	{
		insert_phrase($dim, $phrase); 
	}
	else
	{
		mod_phrase($id, $dim, $phrase); 
	}
}

if( !empty($id) && $affiche )
{
	$do = donne_phrase($id); 
	$dim = $do['dim'];
	$phrase = $do['phrase'];
	$id = $do['id']; 
}

include PATRON;
