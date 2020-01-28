<?php
require '../../include/init.php'; 
require C_INC.'structure_fonc.php'; 

if(!isset($_GET['f']) )
{
	exit('Besoin du paramètre f'); 
}

if( is_numeric($_GET['f']) )
{
	$donne = req('SELECT * FROM structure_facture WHERE id='.(int)$_GET['f']); 

	if( !($do = fetch($donne) ) )
	{
		exit('Aucune facture.'); 
	}

	$fichier = RETOUR.$do['dossier'].'/'.$do['fichier']; 
}
else
{
	$donne = req('SELECT rappel_facture, id AS structure FROM structure WHERE rappel_facture LIKE(\''.secubdd($_GET['f']).'\') '); 

	if( !$do = fetch($donne) )
	{
		exit('Aucune facture.'); 
	}

	$fichier = RETOUR.$do['rappel_facture']; 
}

if (!file_exists($fichier) )
{
	exit('Fichier inexistant'); 
}

if(!( droit(GERER_UTILISATEUR) || str_droit_utilisateur($do['structure']) ) )
{
	exit('Pas les droits'); 
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($fichier).'"');
header('Content-Length: ' . filesize($fichier));
readfile($fichier);
exit;
