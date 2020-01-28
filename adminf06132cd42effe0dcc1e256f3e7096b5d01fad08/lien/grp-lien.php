<?php
require '../../include/init.php'; 
require_once C_INC.'lien_grp_ls.php'; 
require_once C_INC.'lien_grp_fonc.php'; 

http_param( array( 'idm' => 0, 'ids' => array() ) ); 

$lien_grp = new lien_grp_ls; 
$lg = new lien_grp; 

if( isset($_POST['ok']) )
{
	lien_grp_enr( new lien_grp($_POST) ); 
}
elseif( !empty($idm) )
{
	if( $lg = lien_grp_init($idm) )
	{
		$lien_grp->mut_rejet($idm); 
	}
	else
	{
		$lg = new lien_grp; 
	}
}
elseif( isset($_POST['oks'] ) )
{
	lien_grp_sup($ids); 
}

$lien_grp->requete(); 

require PATRON; 
