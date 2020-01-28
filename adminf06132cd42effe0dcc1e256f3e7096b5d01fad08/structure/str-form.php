<?php
require_once '../../include/init.php'; 
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'structure_class.php'; 
require_once C_INC.'structure_fonc.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'contact_fonc.php';
require_once C_INC.'fonc_upload.php'; 
require_once C_INC.'fonc_memor.php'; 
require_once C_INC.'structure_form.php'; 

$PAT->ajt_haut('str-menu_p.php', C_ADMIN.'structure/patron/');

http_param(array('ids' => 0, 'p' => 0) ); 

if( !str_droit_utilisateur($ids) )
{
	page_erreur(); 
}

$traitement = FALSE; 
$form = new structure_form; 

if( isset($_POST['ok']) )
{
	if( $form->valide() )
	{
		$str = $form->donnee(); 
		$traitement = TRUE; 
		str_relais_enr($str); 
	}
}
else
{
	$form->mut_str($ids); 
}

$str = $form->str; 

require PATRON; 
