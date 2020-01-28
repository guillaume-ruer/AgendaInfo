<?php 
include '../../include/init.php'; 
include C_INC.'html_lib.php'; 
include C_INC.'ville_fonc.php'; 
include C_INC.'fonc_upload.php'; 
include C_INC.'fonc_memor.php'; 
include C_INC.'lieu_grp_ls.php';

http_param( array('id' => 0 ) ); 

if( !($ville = ville_init($id) ) )
{
	$ville = new ville(); 
}

$image = new fichier_form( array(
	'nom' => $ville->acc_image(), 
	'dos' => C_IMG.'photos/', 
	'hauteur' => 89, 
	'largeur' => 505, 
) ); 
$traitement = FALSE; 

if( isset($_POST['ok']) )
{
	http_param( array(
		'id'=>0, 'nom'=>'', 'cp' => 0, 'site'=>'', 
		'fb'=>'', 'wp'=>'', 'tel_mairi'=>'', 'tel_ot'=>'', 
		'grp' => array(), 'desc'=>'', 'dep' => 23, 
		'lat' => 0.0, 'long' => 0.0
	) ); 

	$img = $image->donne(); 
	$ville->hydrate( array(
		'id' => $id, 
		'nom' => $nom,
		'cp' => $cp, 
		'site' => $site, 
		'wikipedia' => $wp, 
		'facebook' => $fb,
		'mairie' => $tel_mairi,
		'office' => $tel_ot,
		'image' => $img['nom'],
		'tab_grp' => $grp,
		'desc' => $desc, 
		'dep' => array('num' => $dep ),
		'lat' => $lat,
		'long' => $long, 
	) ); 

	ville_crud()->enr( $ville ); 
	ville_enr_grp($ville); 
	$traitement = TRUE; 
}

if( !$traitement) 
{
	$grp = new lieu_grp_ls(array('mode' => reqo::NORMAL) ); 
	$grp->requete(); 
}

include PATRON; 
