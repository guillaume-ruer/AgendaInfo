<?php
include '../../include/init.php'; 
include C_INC.'grp_symbole_class.php'; 

non_autorise(GERER_SYMBOLE); 

http_param(array('id' => 0 ) ); 

$gs = new grp_symbole;

if(isset($_POST['ok']) )
{
	http_param(array('nom' => '' ) ); 
	$gs->id = $id;
	$gs->nom = $nom; 
	$gs->enr(); 
	mess('La demande a correctement été prise en compte.'); 
}

$gs->init($id); 

include PATRON;
