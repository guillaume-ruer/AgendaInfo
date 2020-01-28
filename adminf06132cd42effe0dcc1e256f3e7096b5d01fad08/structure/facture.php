<?php
require_once '../../include/init.php';
require_once C_INC.'structure_class.php';
require_once C_INC.'structure_facture_class.php'; 

$PAT->ajt_style('facture.css', C_ADMIN.'structure/style/');
$PAT->ajt_haut('str-menu_p.php', C_ADMIN.'structure/patron/');

http_param(array('ids' => 0, 'p' => 0) ); 

if(!str_droit_utilisateur($ids, STR_MODIFIER) )
{
	page_erreur(); 
}

$str = str_init($ids); 

$lsf = new reqo;
$lsf->mut_sorti('structure_facture'); 
$lsf->requete('
    SELECT structure structure__id, id, somme, date, type, dossier, fichier 
    FROM structure_facture 
    WHERE structure='.(int)$str->acc_id().'
    ORDER BY date DESC
'); 

require PATRON; 
