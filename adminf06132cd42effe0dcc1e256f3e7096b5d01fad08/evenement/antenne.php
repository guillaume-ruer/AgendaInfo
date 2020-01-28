<?php

include '../../include/init.php'; 

header('content-type: text/xml ');

if(isset($_GET['ida']) )
{
	$id = (int)$_GET['ida']; 
}
else
{
	exit(); 
}

include C_INC.'contact_fonc.php'; 

$adh = contact_init($id); 

echo <<<START
<?xml version="1.0" encoding="utf-8" ?>
<adherent>
	<nom>{$adh->acc_structure()->acc_nom()}, {$adh->acc_titre()}</nom>
	<site>{$adh->acc_site()}</site>
	<tel>{$adh->acc_tel()}</tel> 
	<mail>{$adh->acc_mail()}</mail>
</adherent>
START;
