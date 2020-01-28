<?php

function lien_init($id)
{
	static $init=NULL, $ville;

	if( is_null($init) )
	{
		$init = prereq('SELECT id, titre, img, url, type FROM lien WHERE id=? '); 
		$ville = prereq('SELECT id_lieu FROM lien_lieu WHERE id_lien=?'); 
		$grp_lieu = prereq('SELECT id_lieu_grp FROM lien_lieu_grp WHERE id_lien=?'); 
	}

	exereq( $init, array($id) ); 

	if( $do = fetch($init) )
	{
		$ob = new lien($do); 
		exereq( $ville, array($id) ); 
		while( $do = fetch($ville) )
		{
			$ob->ajt_lieu( $do['id_lieu'] );
		}

		exereq( $grp_lieu, array($id) ); 
		while( $do = fetch($grp_lieu) )
		{
			$ob->ajt_grp_lieu( $do['id_lieu_grp'] );
		}
		return $ob; 
	}
	else
	{
		return FALSE; 
	}
}

function lien_ins($lien)
{
	static $ins=NULL, $ville=NULL; 

	if( is_null($ins) )
	{
		$pre = prereq('INSERT INTO lien(type, url, img, titre) VALUES(?,?,?,?) '); 
	}
	exereq($pre, array( $lien->acc_type() , $lien->acc_url(), $lien->acc_img(), $lien->acc_titre() ) ); 
	$lien->mut_id( derid() ); 
	lien_lieu_ins($lien); 
	lien_lieu_grp_ins($lien);
	return $lien; 
}

function lien_lieu_ins($lien)
{
	static $lieu=NULL;

	if( is_null($lieu) )
	{
		$lieu = prereq('INSERT INTO lien_lieu(id_lien,id_lieu) VALUES (?,?) '); 
		$sup = prereq('DELETE FROM lien_lieu WHERE id_lien = ? '); 
	}
	exereq($sup, array($lien->acc_id() ) ); 

	foreach( $lien->acc_lieu() as $id )
	{
		exereq($lieu, array( $lien->acc_id(), $id ) ); 
	}
}

function lien_lieu_grp_ins($lien)
{
	static $lieu=NULL;

	if( is_null($lieu) )
	{
		$lieu = prereq('INSERT INTO lien_lieu_grp(id_lien,id_lieu_grp) VALUES (?,?) '); 
		$sup = prereq('DELETE FROM lien_lieu_grp WHERE id_lien = ? '); 
	}
	exereq($sup, array($lien->acc_id() ) ); 

	foreach( $lien->acc_grp_lieu() as $id )
	{
		exereq($lieu, array( $lien->acc_id(), $id ) ); 
	}
}

function lien_maj($lien)
{
	static $maj=NULL, $maj_img;

	if( is_null($maj) )
	{
		$ch = 'titre=:titre, url=:url, type=:type'; 
		$maj = prereq('UPDATE lien SET '.$ch.' WHERE id=:id') ;
		$maj_img = prereq('UPDATE lien SET '.$ch.', img=:img WHERE id=:id ') ;
	}

	$tab_maj = array(
		'id' => $lien->acc_id(),
		'titre' => $lien->acc_titre(), 
		'url' => $lien->acc_url(),
		'type' => $lien->acc_type(),
	); 

	if( $lien->maj_img() )
	{
		$tab_maj['img'] = $lien->acc_img(); 
		exereq( $maj_img, $tab_maj); 
	}
	else
	{
		exereq( $maj, $tab_maj); 
	}

	lien_lieu_ins($lien); 
	lien_lieu_grp_ins($lien);
	return $lien; 
}

function lien_enr($lien)
{
	$lien->acc_id() == 0 ? lien_ins($lien) : lien_maj($lien); 	
}

function lien_sup($id)
{
	if( is_numeric($id) )
	{
		$id = (int)$id; 
	}
	elseif( is_array($id) )
	{
		$id = implode(',', array_map('intval', $id) ); 
	}
	else
	{
		return FALSE; 
	}

	if( !empty($id) )
	{
		req('DELETE FROM lien WHERE id IN('.$id.')'); 
	}
}
