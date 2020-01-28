<?php
include '../../include/init.php'; 
include C_INC.'lieu_grp_ls.php'; 

if( isset($_GET['id']) )
{
	req('DELETE FROM Lieu_grp WHERE id='.(int)$_GET['id'].' LIMIT 1 '); 
	mess('Groupe de lieu supprimé.'); 
}

http_param( array('p' => 0) ); 

$grp_ls = new lieu_grp_ls( array( 
	'pagin' => array('num_page'=> $p, 'url' => 'grp-lieu.php?p=%pg'),
) ); 
$grp_ls->requete(); 



include PATRON; 
