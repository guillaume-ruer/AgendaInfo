<?php

$traitement = FALSE; 
if(!isset($str) )
{
	$str = new structure; 
}

http_param(array('ids'=>0, 's_nom'=>'', 's_ville' => 0, 's_addr' => '', 's_mail' => '', 's_mail_rq' => '', 'type' => '', 'idcsup' => array(), 
	'presentation' => '', 'tel' => array(), 'titre' => array(), 'site' => array(), 'idc' => array(), 's_actif' => 0, 
	's_sup_logo' => '', 's_numero' => 0 
) ); 

$s_nom=trim($s_nom);

if(!empty($s_nom) )
{
	// Champs de base 
	$str->hydrate(array(
		'id' => $ids,
		'nom' => $s_nom, 
		'adresse' => array( 
			'ville' => array('id' => $s_ville), 
			'rue' => $s_addr
		),
		'mail' => $s_mail,
		'mail_rq' => $s_mail_rq, 
		'desc' => $presentation 
	) ); 

	// Uniquement les champs modifiable avec des droits 
	if(droit(GERER_UTILISATEUR ) )
	{
		$str->mut_type($type);
		$str->mut_actif($s_actif); 
		$str->mut_numero($s_numero); 
	}

	if( $s_sup_logo )
	{
		$str->mut_logo(''); 
	}

	$str->mut_logo('s_logo', TRUE); 

	$idc = array_diff($idc, $idcsup); 
	contact_sup($idcsup); 

	foreach($idc as $i => $id )
	{
		$c = new contact(array(
			'id' => $id, 
			'titre' => $titre[$i], 
			'tel' => $tel[$i],
			'site' => $site[$i],
		) ); 
		$str->ajt_contact($c); 
	}

	str_enr($str); 
	$traitement = TRUE; 
}
