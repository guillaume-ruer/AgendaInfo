<?php
require_once C_INC.'contact_class.php'; 

function contact_init($id)
{
	$req = req('
		SELECT sc.id, titre, tel, site, nom, id_structure, email FROM structure_contact sc
		LEFT JOIN structure s
			ON s.id = sc.id_structure
		WHERE sc.id='.absint($id).'
		LIMIT 1 
	'); 
	$do = fetch($req); 
	$c = new contact(array(
		'id' => $do['id'], 
		'structure' => array('id' => $do['id_structure'], 'nom' => $do['nom'] ), 
		'titre' => $do['titre'], 
		'tel' => $do['tel'], 
		'site' => $do['site'],
	) ); 
	return $c; 
}

function contact_maj($c)
{
	static $maj=NULL; 
	if( is_null($maj) )
	{
		$maj = prereq('UPDATE structure_contact SET titre=?, tel=?, site=? WHERE id=?'); 
	}

	exereq($maj, array($c->acc_titre(), $c->acc_tel(), $c->acc_site(), $c->acc_id() ) ); 
	return $c; 
}

function contact_enr($c)
{
	$c->acc_id() ? contact_maj($c) : contact_ins($c); 
}

function contact_ins($c)
{
	static $p=NULL;

	if(is_null($p) )
	{
		$p = prereq('INSERT INTO structure_contact( id_structure, site, tel, titre ) VALUES(?,?,?,?) ');
	}

	exereq($p, array($c->acc_structure()->acc_id(), $c->acc_site(), $c->acc_tel(), $c->acc_titre() ) ); 
	$c->mut_id( derid() );
	return $c; 
}


function contact_sup($id)
{
	if(is_array($id) )
	{
		if( !empty($id) )
		{
			$id = implode(',', array_map('absint', $id) );
		}
	}
	else
	{
		$id = absint($id); 
	}

	if(!empty($id) )
	{
		req('DELETE FROM structure_contact WHERE id IN('.$id.')');
	}
}

function contact_sup_not_str($id, $ids)
{
	if(is_array($id) )
	{
		if( !empty($id) )
		{
			$id = implode(',', array_map('absint', $id) );
		}
	}
	else
	{
		$id = absint($id); 
	}

	req('DELETE FROM structure_contact 
		WHERE '.(!empty($id) ? 'id NOT IN('.$id.') AND ' : '' ).' 
		id_structure='.absint($ids) );
}
