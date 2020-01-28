<?php
require '../../include/init.php'; 

header('Content-type: application/json'); 

if( !isset($_GET['term']) )
{
	exit(); 
}

$term = trim($_GET['term']); 

if( strlen($term) < 2 )
{
	exit(); 
}

$ville = new reqa('
	SELECT absint::Lieu_ID id, secuhtml::Lieu_Ville ville, absint::Lieu_Dep dep
	FROM Lieu 
	WHERE Lieu_Ville LIKE(\''.secubdd($term).'%\')
	ORDER BY Lieu_Ville, Lieu_Dep 
	LIMIT 50
'); 

$res = [];

while($v = $ville->parcours() )
{
	$res[] = ['value' => $v->ville, 'label' => $v->ville.' ('.$v->dep.')', 'id' => $v->id ]; 
}

echo JSON_encode($res); 
