<?php
require '../include/init.php'; 

$res = []; 

if( isset($_GET['nom']) )
{
	$nom = $_GET['nom']; 
	$nom = str_replace([' '], ['-'], $nom); 

	$donne = req('
		SELECT Lieu_ID id, Lieu_Ville nom, image1, Lieu_Dep dep__num
		FROM Lieu 
		WHERE Lieu_Ville LIKE(\''.secubdd($nom).'%\')
		LIMIT 50
	'); 

	while($do = fetch($donne) )
	{
		$ville = new ville(genere_init($do) ); 
		$res[] = [
			'id' => $do['id'],
			'proposition' => $ville->proposition(),
			'etiquette' => $ville->etiquette(), 
			'json' => $ville->json(),
		]; 
	}
}

echo JSON_encode($res); 
