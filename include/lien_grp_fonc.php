<?php

function lien_grp_init($id)
{
	static $init=NULL;
	
	if( is_null($init) )
	{
		$init = prereq('SELECT id, nom FROM lien_grp WHERE id=?'); 
	}

	exereq($init, array($id) ); 

	if( $do = fetch($init) )
	{
		$ob = new lien_grp($do); 
		return $ob; 
	}
	else
	{
		return FALSE; 
	}
}

function lien_grp_ins($lg)
{
	static $ins=NULL;

	if( is_null($ins) )
	{
		$ins = prereq('INSERT INTO lien_grp(nom) VALUES (?) '); 
	}

	exereq($ins, array( $lg->acc_nom() ) ); 
}

function lien_grp_maj($lg)
{
	static $maj=NULL; 

	if( is_null($maj) )
	{
		$maj = prereq('UPDATE lien_grp SET nom=? WHERE id=? '); 
	}

	exereq($maj, array($lg->acc_nom(), $lg->acc_id() ) ); 
}

function lien_grp_enr($lg)
{
	$lg->acc_id() == 0 ? lien_grp_ins($lg) : lien_grp_maj($lg); 	
}

function lien_grp_sup($id)
{
	if( is_numeric($id) )
	{
		$id = (int)$id; 
	}
	elseif( is_array($id) )
	{
		$id = implode(',', array_map('intval', $id ) ); 
	}
	else
	{
		return FALSE; 
	}

	if( !empty($id) )
	{
		
		req('DELETE FROM lien_grp WHERE id IN('.$id.')');
	}
}
