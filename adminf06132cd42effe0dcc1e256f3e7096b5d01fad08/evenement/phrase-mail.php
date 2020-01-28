<?php
include '../../include/init.php'; 
include 'include/phrase_mail_fonc.php'; 
include C_INC.'reqa_class.php'; 

if( isset($_GET['id'] ) ) 
{
	sup_phrase($_GET['id']); 
}

$ls = liste_phrase(); 

include PATRON; 
