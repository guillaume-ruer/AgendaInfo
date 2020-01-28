<?php

/*
	droit : 
		ajt : valeur du droit necessaire pour ajouter un article. 
		Laissez à 0 si aucun droit necessaire. 
		mod : valeur du droit necessaire pour modifier les articles.
		Le créateur d'un article peut le modifier. 
		Si laissez à 0, personne ne peut modifier à part le créateur.
		etat : valeur des droits necessaire pour utiliser l'état de l'article. 
		com : droit pour gerer (supprimmer, modifier) les commentaires. 

	nom : nom du type d'article. 
	etat : etat possible d'un article. Si pas d'état voulu, laissez vide. 
	L'état par défaut sera le premier de la liste.
	const : le nom d'une constante qui prendra pour valeur l'id du type d'article. 
*/


$ARTICLE_CONF = array(
	array(
		'droit' => array(
			'ajt' => GERER_ARTICLE, 
			'mod' => GERER_ARTICLE,
			'com' => GERER_ARTICLE
		),
		'nom' => 'Actualité',
		'etat' => array(),
		'const' => 'NOUVELLE'
	),
);

