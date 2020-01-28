<?php
include '../../include/init.php'; 
include C_INC.'symbole_class.php';
include C_INC.'reqa_class.php'; 
include C_INC.'fonc_upload.php'; 
include C_INC.'fonc_memor.php'; 

non_autorise(GERER_SYMBOLE); 

http_param( array('id' => 0) ); 

$s = new symbole; 

$lsgroupe=ls_grp_symbole(); 

list($width, $height,$redim) = rappel('sym-form', [64,64,TRUE]); 

if(isset($_POST['ok']) )
{
	http_param(array('nom' => '', 'groupe' => 0,'width'=>$width,'height'=>$height,'redim' =>$redim) ); 
	memor('sym-form', [$width, $height,$redim]); 

	$s->id = $id;
	$s->id_groupe = $groupe; 
	$s->nom = $nom; 
	$s->width = $width;
	$s->height = $height; 

	if(!$redim)
	{
		$s->img = tcimg('img', C_IMG.'symboles/', NULL); 
	}
	else
	{
		$s->img = tcimg('img', C_IMG.'symboles/', NULL, $width, $height, $redim); 
	}

	$s->enr(); 
	mess('La demande a correctement été prise en compte.'); 
}

if(!empty($id) )
{
	if( !$s->init($id) )
	{
		mess('Aucun symbole ne correspond à l\'identifiant donné.');
	}
}

include PATRON; 
