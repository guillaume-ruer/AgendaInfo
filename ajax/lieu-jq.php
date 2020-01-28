<?php
require '../include/init.php'; 

$res = []; 

if( isset($_GET['term']) )
{
	$nom = $_GET['term']; 
	$nom = str_replace([' '], ['-'], $nom); 

	$donne = req('
		SELECT Lieu_ID id, Lieu_Ville nom, image1, Lieu_Dep
		FROM Lieu 
		WHERE Lieu_Ville LIKE(\''.secubdd($nom).'%\')
		LIMIT 50
	'); 

	while($do = fetch($donne) )
	{
		$res[] = [
			'value' => $do['nom'].' ('.(int)$do['Lieu_Dep'].')', 
			'id' => $do['id'], 
		]; 
	}
}

echo JSON_encode($res); 
