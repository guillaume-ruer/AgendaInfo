<?php
require_once '../../include/init.php'; 

require_once C_INC.'fonc_memor.php';
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'structure_class.php'; 
require_once C_INC.'structure_fonc.php'; 

$PAT->ajt_haut('str-menu_p.php', C_ADMIN.'structure/patron/');

if( !droit(GERER_UTILISATEUR) )
{
	page_erreur(); 
}

http_param(array('ids' => 0) ); 

$donne = req('SELECT * FROM structure WHERE id='.$ids.' LIMIT 1 ');

if( !($str = fetch($donne) ) )
{
	$str['id'] = 0; 
	$str['pdf_haut'] = rappel('pdf_haut'); 
	$str['pdf_bas'] = rappel('pdf_bas'); 
}

$dos = C_DOS_PHP.'pdf/'; 

if( isset($_POST['ok']) )
{

	if( !file_exists($dos) )
	{
		mkdir($dos); 
	}

	$ent = $str['pdf_haut']; 
	if( $tmp_ent = tcimg('ent', $dos, 'jpg,jpeg', 2480, 572, TRUE) )
	{
		$ent = $tmp_ent;
	}
	elseif( isset($_POST['ent_sup']) )
	{
		$ent = ''; 
	}

	$pied = $str['pdf_bas']; 
	if( $tmp_pied = tcimg('pied', $dos, 'jpg,jpeg', 2480, 372, TRUE) )
	{
		$pied = $tmp_pied; 
	}
	elseif( isset($_POST['pied_sup']) )
	{
		$pied = ''; 
	}

	if( $str['id'] == 0 )
	{
		memor('pdf_haut', $ent); 
		memor('pdf_bas', $pied); 
	}
	else
	{
		req('
			UPDATE structure 
			SET pdf_haut=\''.secubdd($ent).'\', pdf_bas=\''.secubdd($pied).'\' 
			WHERE id='.$ids.' LIMIT 1
		');
	}

	$str['pdf_haut'] = $ent; 
	$str['pdf_bas'] = $pied; 
}

require PATRON; 
