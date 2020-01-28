<?php

if( ! droit( ADMIN ) )
{
 	senva(); 
}

define('HAUT_ADMIN', C_ADMIN.'patron/haut.admin.php'); 
define('BAS_ADMIN', C_ADMIN.'patron/bas_admin.php'); 
define('PAT_ERREUR', C_ADMIN.'patron/erreur.php'); 

include C_INC.'visuel_liste_class.php'; 

$TAB_ETAT = array(1 => 'Actif', 2 => 'Supprimé', 0 => 'Masqué' );

function non_autorise($droit )
{
	if(!droit($droit) )
	{
		page_erreur(); 
	}
}

function page_erreur()
{
	global $PAT, $TAB_SCRIPT, $TAB_STYLE, $NB_PRE, $NB_REQ, $NB_EXE, $ARTICLE_CONF, $VISUEL_CONF; 	
	$PAT->def_patron(); 
	$PAT->ajt_patron('erreur.php', C_ADMIN.'patron/'); 
	include PATRON; 
	exit(); 
}

include C_ADMIN.'include/article_conf.php'; 
include C_ADMIN.'include/visuel_conf.php'; 
include C_INC.'article_fonc.php'; 

$PAT->def_bas();
$PAT->ajt_haut( 'menu_admin.php', C_ADMIN.'patron/'); 
$PAT->ajt_bas( 'bas_admin.php', C_ADMIN.'patron/'); 
$PAT->def_style();
$PAT->ajt_style('admin.css'); 
$PAT->ajt_script('jquery-3.1.1.min.js');
$PAT->ajt_script('menu-admin.js', C_ADMIN.'javascript/');

ajt_script('jquery-3.1.1.min.js'); 
ajt_script('menu-admin.js',C_ADMIN.'javascript/'); 
