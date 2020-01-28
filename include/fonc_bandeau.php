<?php

function recupe_bandeau($type, $nb, $pagin = FALSE, &$lien = ''  )
{
	//Récupération des bandeau 
	if(!$pagin )
	{ 
		$donne = req('
			SELECT id, Image, URL, Titre, Texte  
			FROM Bandeaux 
			WHERE DateDeb <= NOW() 
			AND DateFin >= NOW() 
			AND Type = \''.secubdd($type).'\' 
			ORDER BY RAND() 
			LIMIT '.(int)$nb.' 
		');
	}
	else
	{
		$donne = reqp('
			SELECT id, Image, URL, Titre, Texte  
			FROM Bandeaux 
			WHERE DateDeb <= NOW() 
			AND DateFin >= NOW() 
			AND Type = \''.secubdd($type).'\' 
			ORDER BY DateDeb 
		', $lien, $nb );
	}

	//Initialisation de la boucle 
	$bandeau = array();
	$tab_id = array(); 

	//Création du tableau à envoyer dans le code html 
	while($do = fetch($donne ) )
	{
		$bandeau[] = array(
			'id' => (int)$do['id'],
			'image' => 'http://info-limousin.com/img/bandeaux/'.secuhtml($do['Image']),
			'url' => secuhtml($do['URL']),
			'titre' => secuhtml($do['Titre']),
			'texte' => secuhtml($do['Texte'])
		);
		
		$tab_id[] = (int)$do['id'];
	}

	//Incrémentation du conteur d'affichage des affichettes 
	if(!empty($tab_id) )
	{
		req('UPDATE `Bandeaux` SET `Affichages`= Affichages + 1  WHERE `id` IN( '.implode(', ', $tab_id).') LIMIT '.(int)$nb.' ');
	}

	return $bandeau; 
}

