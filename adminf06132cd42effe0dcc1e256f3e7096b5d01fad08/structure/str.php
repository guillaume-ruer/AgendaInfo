<?php
require_once '../../include/init.php';
require_once C_INC.'structure_class.php';
require_once C_INC.'structure_fonc.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_ADMIN.'structure/include/tab-type-str.php'; 

$PAT->ajt_haut('str-menu_p.php', C_ADMIN.'structure/patron/');
ajt_style('str.css'); 

http_param(array('ids' => 0, 'p' => 0) ); 

$str = str_init($ids); 

$strtype = ''; 

foreach($tab_type as $type )
{
	if($type[0] == $str->acc_type() )
	{
		$strtype = $type[1]; 
	}
}

$cout_annuel = $str->cout_annuel();  

$tab_opt = $str->abo_option(); 

if( $str->acc_id_paypal() == '' )
{
	$str->mut_id_paypal( code_aleat(40) ); 
	req('UPDATE structure SET id_paypal=\''.$str->acc_id_paypal().'\' WHERE id='.(int)$str->acc_id().' LIMIT 1');
}

$donne = req('
	SELECT rappel
	FROM structure_droit 
	WHERE structure = '.$str->acc_id().'
	AND utilisateur = '.$MEMBRE->id.'
');

$do_droit = fetch($donne); 

if( isset($_GET['rp']) )
{
	$rappel = (bool)$_GET['rp']; 

	if( $do_droit)
	{
		req('
			UPDATE structure_droit SET rappel='.(int)$rappel.' 
			WHERE structure = '.$str->acc_id().'
			AND utilisateur = '.$MEMBRE->id.'
		'); 
		$do_droit['rappel'] = $rappel; 
	}
	else
	{
		req('
			INSERT INTO structure_droit(structure,utilisateur,rappel)
				VALUES('.(int)$str->acc_id().','.(int)$MEMBRE->id.','.(int)$rappel.')
		');
		$do_droit = [ 'rappel'=> $rappel ]; 
	}
}

if($do_droit)
{
	$rappel = (bool)$do_droit['rappel']; 
	$gerant = TRUE; 
}
else
{
	$gerant = $str->acc_id() == $MEMBRE->id_structure; 
	$rappel = $gerant; 
}

require PATRON; 
