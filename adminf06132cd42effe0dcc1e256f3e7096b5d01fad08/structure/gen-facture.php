<?php
require '../../include/init.php'; 
require_once C_INC.'facture_pdf_fonc.php'; 
require_once C_INC.'structure_facture_fonc.php'; 
require_once C_INC.'structure_class.php';

if( !droit(GERER_UTILISATEUR) )
{
	exit(); 
}

$str = new structure(['id' => $_GET['id'] ]); 
$dos = structure_facture_dossier();
$dof = genere_facture_pdf($str->acc_id(), $dos);

$fichier = $dof['dos'].'/'.$dof['fichier']; 

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($fichier).'"');
header('Content-Length: ' . filesize($fichier));
readfile($fichier);

