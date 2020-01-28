<?php
include '../../../include/init.php'; 

if( isset($_GET['id'], $_GET['date']) )
{
	list($j,$m,$a)=explode('/', $_GET['date']); 
	$time = mktime(0,0,0,$m,$j,$a); 
	$pre = prereq('UPDATE structure SET date_fin_adhesion=?, rappel=0, rappel_facture=\'\' WHERE id=?');
	exereq($pre, [ $time, $_GET['id'] ]); 
	echo 'ok'; 
}
else
{
	echo 'fail'; 
}
