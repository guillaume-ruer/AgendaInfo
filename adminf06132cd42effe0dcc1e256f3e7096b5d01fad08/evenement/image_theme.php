<?php

include '../../include/init.php'; 

header('content-type: text/xml ');

if(isset($_GET['idi']) )
{
	$id = (int)$_GET['idi']; 
}
else
{
	exit(); 
}

$donne = req('SELECT CAT_IMG image, width, height FROM Categories WHERE CAT_ID='.$id.' LIMIT 1 '); 
$do = fetch($donne); 

$image = empty($do['image']) ? 'divers-vide.png' : secuhtml($do['image']); 
$height = (int)$do['height']; 
$width = (int)$do['width']; 

echo <<<START
<?xml version="1.0" encoding="utf-8" ?>
<theme>
	<image>{$image}</image>
	<width>{$width}</width>
	<height>{$heihgt}</height>
</theme>
START;
