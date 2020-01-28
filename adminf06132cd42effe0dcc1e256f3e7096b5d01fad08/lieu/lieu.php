<?php
include '../../include/init.php'; 
require_once C_INC.'lieu_ls.php'; 

http_param( array('l' => 'a' ) ); 

if( isset($_GET['id']) )
{
	req('DELETE FROM Lieu WHERE Lieu_ID='.(int)$_GET['id'].' LIMIT 1');
	mess('Lieu supprimé.'); 
}

$lieux = new lieu_ls( array('fi_debut'=>$l) ); 
$lieux->requete(); 

include PATRON;
