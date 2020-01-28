<?php
include '../../include/init.php'; 
include C_INC.'reqa_class.php'; 

$annee = isset($_POST['annee']) ? (int)$_POST['annee'] : (int)date('Y') ; 
$moi = isset($_POST['moi']) ? (int)$_POST['moi'] : ''; 

$chemin = C_DOS_PHP.'archives/'; 
$fichier = 'archive-'.$annee.'.xml'; 

if(file_exists($chemin.$fichier) )
{
	/*
		Crée le générateur xslt 
	*/
	$archive = new DOMDocument(); 
	$archive->load($chemin.$fichier); 

	$docxsl = new DOMDocument(); 
	$docxsl->load('include/lecture-archive.xsl'); 

	$xsl = new XSLTProcessor(); 
	$xsl->importStyleSheet($docxsl); 
	
		
	include HAUT_ADMIN; 
	include C_PATRON_STAT.'archive.php';
	include BAS_ADMIN;
}
else
{

	/* Lei : */
	$stat_lei = stat_lei($annee, $moi ); 

	/* Nombre d'événement total de l'année */ 
	$evenement_total = nombre_evenement($annee, $moi );

	/* Nombre d'événement par département */ 
	$departement = stat_dep($annee, $moi ); 

	/* Nombre d'événement par contact */
	$contact = stat_contact($annee, $moi );  

	/* Nombre d'événements par categorie */
	$categorie = stat_categorie($annee, $moi ); 

	/* Nombre d'événements par theme */
	$theme = stat_theme($annee, $moi );

	/* Nombre d'événements mis en actif par modérateur */
	$moderateur = stat_moderateur($annee, $moi );

	include HAUT_ADMIN; 
	include C_PATRON_STAT.'stat.p.php'; 
	include BAS_ADMIN;
}
