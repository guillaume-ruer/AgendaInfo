<?php


if( FICHIER != 'facture' )
{
	non_autorise(GERER_UTILISATEUR); 
}

/*
	Ajout d'un droit? 
	Ajouté simplement une ligne
*/
$TAB_DROIT = array();

function tab_droit_ajt($nom, $petit, $bit, $desc='' )
{
	global $TAB_DROIT;
	$TAB_DROIT[] = array('nom' => $nom, 'petit' => $petit, 'bit' => $bit, 'desc' => $desc); 
}

tab_droit_ajt("Accès administration", 'Admin', ADMIN, 'Droit d\'accès à l\'administration.'); 
tab_droit_ajt("Changer le fond du site", 'Fond', CHANGER_FOND, 'Changer l\'image de fond de l\'agenda.' ); 
tab_droit_ajt("Gérer les utilisateurs", 'Utilisateur', GERER_UTILISATEUR, 'Voir et modifier les utilisateurs.' ); 
tab_droit_ajt("Gérer les préfix des titres", 'Préfix', PREFIX, "Modifier la liste des préfixes des titres des événements." ); 
tab_droit_ajt("Mettre un événement en Actif", 'Actif', MODIF_ETAT, "Activer un événements pour qu'il soit visible dans l'agenda." ); 
tab_droit_ajt("Gérer tout les événements", 'Tout', GERER_EVENEMENT, "Gérer l'ensemble des évenements du site." ); 
tab_droit_ajt("Voir toutes les stats", 'Stat', TOUT_STAT, "Accès à toutes les statistiques du site." );
tab_droit_ajt("Gérer lei (correspondance, log, alerte)", 'LEI', GERER_LEI, "Gérer tout ce qui concerne le LEI.");
tab_droit_ajt("Gérer les suivis", 'Suivis', GERER_ARTICLE, "Gérer les suivs." );
tab_droit_ajt("Gérer les symboles", 'Symboles', GERER_SYMBOLE, "Gérer les catégories (symboles) des évenements." );
tab_droit_ajt("Gérer les visuels", 'Visuels', GERER_VISUEL, "Gérer tout les visuels." );
tab_droit_ajt('Gérer les lieux', 'Lieux', GERER_LIEU, "Gérer les lieux et groupes de lieux."); 
