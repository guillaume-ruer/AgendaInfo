<?php
define('VISUEL_VILLE_NON', 0); 
define('VISUEL_VILLE_UNE', 1); 
define('VISUEL_VILLE_LISTE', 2); 

$VISUEL_CONF = array(
/*
	array( 
		'nom' => 'Affichettes',
		'page' => 'affichettes', 
		'type' => 'Affichette',
		'texte' => 'Aléatoirement, 5 affichettes s\'affichent en haut de toutes les pages de l\'agenda.',
		'titre' => 'Liste des affichettes',
		'nouveau' => 'Ajouter une affichette', 
		'ville' => VISUEL_VILLE_NON,
		'date' => TRUE,
		'contenu' => TRUE,
		'structure' => TRUE, 
		'contact' => TRUE,
		'img' => array(
			'haut' => 232,
			'large' => 164,
		),
		'const' => 'AFFICHETTE', 
		'droit' => 0, 
	),
	0 => array( 
		'nom' => 'Expositions',
		'page' => 'expositions',
		'type' => 'expo',
		'texte' => 'Les expositions sont visibles sur la page principale de l\'agenda dans la colonne de droite,
			sur la page de la ville sélectionnée ainsi que celle de la structure.',
		'titre' => 'Liste des expositions',
		'nouveau' => 'Ajouter une exposition', 
		'ville' => VISUEL_VILLE_UNE,
		'date' => TRUE,
		'contenu' => TRUE,
		'structure' => TRUE, 
		'contact' => FALSE,
		'img' => array(
			'haut' => 170,
			'large' => 120,
		),
		'const' => 'EXPO', 
		'droit' => 0,

	),
	1 => array( 
		'nom' => 'Communications',
		'type' => 'pub',
		'page' => 'communications',
		'texte' => 'Les communications sont visibles sur la page de la structure, et dans les pages des villes sélectionnées.',
		'titre' => 'Liste des communications',
		'nouveau' => 'Ajouter une communication', 
		'ville' => VISUEL_VILLE_LISTE,
		'date' => TRUE,
		'contenu' => TRUE,
		'structure' => TRUE, 
		'contact' => FALSE,
		'img' => array(
			'haut' => 170,
			'large' => 120,
		),
		'const' => 'PUB', 
		'droit' => 0,
	),

*/
/*	array( 
		'nom' => 'Bannières',
		'type' => 'banniere',
		'page' => 'bannieres',
		'texte' => 'Les bannières sont visible sur la page de la structures, et dans la page de la ville de la structure.',
		'titre' => 'Liste des bannières', 
		'nouveau' => 'Ajouter une bannière', 
		'ville' => VISUEL_VILLE_NON,
		'date' => FALSE,
		'contenu' => FALSE,
		'structure' => TRUE, 
		'contact' => FALSE,
		'img' => array(
			'haut' => 60,
			'large' => 120,
		),
		'const' => 'BANNIERE', 
		'droit' => 0,
	), 
	3 => array( 
		'nom' => 'Télé Millevaches',
		'type' => 'Video',
		'page' => 'tele-millevaches',
		'texte' => 'Afficher uniquement sur la page d\'index.',
		'titre' => 'Liste des Télé millevaches',
		'nouveau' => 'Ajouter une Télé millevaches', 
		'ville' => VISUEL_VILLE_NON,
		'date' => FALSE,
		'contenu' => TRUE,
		'structure' => FALSE, 
		'contact' => FALSE,
		'img' => array(
			'haut' => 96,
			'large' => 120,
		),
		'const' => 'TELE', 
		'droit' => GERER_VISUEL, 
	),
	4 => array( 
		'nom' => 'L\'esprit village',
		'type' => 'Journal',
		'page' => 'esprit-village',
		'texte' => 'Afficher uniquement sur la page d\'index.',
		'titre' => 'Liste des Esprit Village',
		'nouveau' => 'Ajouter un Esprit Village', 
		'ville' => VISUEL_VILLE_NON,
		'date' => TRUE,
		'contenu' => TRUE,
		'structure' => FALSE, 
		'contact' => FALSE,
		'img' => array(
			'haut' => 120,
			'large' => 96,
		),
		'const' => 'ESPRIT', 
		'droit' => GERER_VISUEL, 
	),
	5 => array( 
		'nom' => 'Journal IPNS',
		'type' => 'Letter',
		'page' => 'journal-ipns',
		'texte' => 'Afficher uniquement sur la page d\'index.',
		'titre' => 'Liste des Jounal IPNS',
		'nouveau' => 'Ajouter un journal IPNS', 
		'ville' => VISUEL_VILLE_NON,
		'date' => FALSE,
		'contenu' => TRUE,
		'structure' => FALSE, 
		'contact' => FALSE,
		'img' => array(
			'haut' => 170,
			'large' => 120,
		),
		'const' => 'IPNS', 
		'droit' => GERER_VISUEL, 
	),
// */
	6 => array( 
		'nom' => 'Bandeaux agenda dynamique',
		'type' => 'Banderol',
		'page' => 'Bandeaux',
		'texte' => 'Bandeaux du haut',
		'titre' => 'Bandeaux',
		'nouveau' => 'Ajouter un bandeau', 
		'ville' => VISUEL_VILLE_NON,
		'date' => FALSE,
		'contenu' => TRUE,
		'structure' => FALSE, 
		'contact' => FALSE,
		'img' => array(
			'haut' => NULL,
			'large' => NULL,
		),
		'const' => 'BANDEROL', 
		'droit' => GERER_VISUEL, 
		'dos' => 'banderol/',
	),
); 
