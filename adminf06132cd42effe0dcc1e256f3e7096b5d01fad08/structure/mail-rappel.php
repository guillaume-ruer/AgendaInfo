<?php
require '../../include/init.php'; 
require C_INC.'fonc_memor.php'; 

$def = include 'include/mail-defaut.php'; 

$phrase = rappel('mail-rappel', $def); 

if( isset($_POST['save_id'], $_POST['save_txt']) )
{
	$phrase[ (int)$_POST['save_id'] ][1] = $_POST['save_txt']; 
	$phrase[ (int)$_POST['save_id'] ][2] = $_POST['save_sujet']; 
	memor('mail-rappel', $phrase); 
	exit(); 
}

$PAT->ajt_script('ckeditor/ckeditor.js'); 
$PAT->ajt_script('ckeditor/adapters/jquery.js'); 

require PATRON;
