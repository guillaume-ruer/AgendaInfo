<?php
require '../../include/init.php'; 

$mail = $MEMBRE->mail;
$cr = $MEMBRE->compte_rendu; 
$notif = $MEMBRE->notif; 
$maj = FALSE; 

if( isset($_POST['ok']) )
{
	$MEMBRE->compte_rendu = isset($_POST['cr']); 
	$MEMBRE->notif = isset($_POST['notif']); 
	$MEMBRE->mail = $_POST['mail']; 

	if( $MEMBRE->maj_public() )
	{
		$maj = TRUE; 
	}
}

require PATRON; 
