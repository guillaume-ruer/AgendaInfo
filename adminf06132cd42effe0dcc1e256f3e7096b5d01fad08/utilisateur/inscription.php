<?php
include '../../include/init.php';
include C_INC.'fonc_memor.php'; 

$ins_ouvert = (bool)rappel('inscription'); 

if( isset($_POST['ok']) )
{
	$ins_ouvert = !$ins_ouvert; 
	memor('inscription', $ins_ouvert); 
}

include PATRON; 
