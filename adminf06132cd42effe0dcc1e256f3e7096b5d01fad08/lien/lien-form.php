<?php
require '../../include/init.php'; 
require_once C_INC.'lien_class.php'; 
require_once C_INC.'lien_grp_ls.php'; 
require_once C_INC.'lien_fonc.php'; 
require_once C_INC.'fonc_upload.php'; 
require_once C_INC.'fonc_memor.php'; 
require_once C_INC.'lieu_grp_ls.php'; 

http_param( array('id' => 0 ) ); 

$lien = new lien; 
$valide=FALSE; 

if( isset($_POST['ok']) )
{
	$lien = new lien($_POST); 

	if( !file_exists(C_LIEN_IMG) )
	{
		mkdir(C_LIEN_IMG, 0777, TRUE); 
	}

	if( $image = tcimg('img', C_LIEN_IMG)  )
	{
		$lien->mut_img($image); 
	}
	elseif( isset($_POST['sup']) )
	{
		$lien->mut_img(''); 
	}

	lien_enr($lien); 
	$valide=TRUE; 
}
elseif( !empty($id) )
{
	if(!($lien = lien_init($id) ) )
	{
		$lien = new lien; 
	}
}

$lien_grp = new lien_grp_ls; 
$lien_grp->requete(); 

$ville = new reqo(array('sorti' => 'id_nom', 'mode' => reqo::NORMAL ) ); 
$ville->requete('SELECT Lieu_ID id, Lieu_Ville nom FROM Lieu ORDER BY Lieu_Ville ');

$grp = new lieu_grp_ls( array('mode' => reqo::NORMAL ) );
$grp->requete(); 

require PATRON; 
