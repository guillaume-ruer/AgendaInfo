<?php
require '../../include/init.php'; 
require_once C_INC.'lien_class.php'; 
require_once C_INC.'lien_fonc.php'; 
require_once C_INC.'lien_ls.php'; 
require_once C_INC.'lien_grp_ls.php'; 

http_param(array('grp' => 0, 'ids' => array() ) ); 

if( isset($_POST['oks']) )
{
	lien_sup($ids); 
}

$lien = new lien_ls( array('type' => $grp ) ); 
$lien->requete(); 

require PATRON; 
