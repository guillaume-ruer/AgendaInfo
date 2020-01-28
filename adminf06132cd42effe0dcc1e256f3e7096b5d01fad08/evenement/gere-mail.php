<?php

include '../../include/init.php'; 
include C_INC.'fonc_memor.php'; 

$haut = secuhtml(rappel('mail-haut') );
$bas = secuhtml( rappel('mail-bas') ); 
$sujet =secuhtml( rappel('mail-sujet') ); 

if(isset($_POST['ok']) )
{
	$haut = $_POST['haut']; 
	$bas = $_POST['bas']; 
	$sujet = $_POST['sujet']; 
	memor('mail-haut', $haut ); 
	memor('mail-bas', $bas ); 
	memor('mail-sujet', $sujet ); 
	mess('Les modifications ont été prise en compte.'); 
}

require PATRON; 
