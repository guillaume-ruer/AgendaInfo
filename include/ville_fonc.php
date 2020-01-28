<?php
require_once C_INC.'ville_class.php'; 

function ville_init($id)
{
	$donne = req('
		SELECT Lieu_ID id, Lieu_Ville as nom, Lieu_Dep dep, google_map, Lieu_cp cp,
			mairie, office, internet site, facebook, wikipedia, image1 image, 
			commentaire `desc`, lat, lng
		FROM Lieu l
		WHERE l.Lieu_ID='.(int)$id. ' 
		LIMIT 1 
	');
	
	if( $do = fetch($donne) )
	{
		$do['dep'] = array('num' => $do['dep']); 
		$do['long'] = $do['lng'];
		unset($do['lng']); 
		$v = new ville($do); 

		$donne = req('SELECT id_groupe FROM Lieu_join WHERE id_lieu='.(int)$v->acc_id().' '); 

		while( $do = fetch($donne) )
		{
			$v->add_grp($do['id_groupe']);
		}

		return $v; 
	}
	else
	{
		return FALSE; 
	}
}

function ville_crud()
{
	static $c=NULL; 

	if( is_null($c) )
	{
		$c = new crud( array( 
			crud_ch('id', 'Lieu_ID', crud::TOUT), 
			crud_ch('nom', 'Lieu_Ville', crud::TOUT),
			crud_ch('cp', 'Lieu_cp', crud::TOUT),
			'mairie', 
			'office', 
			crud_ch('site', 'internet', crud::TOUT),
			'facebook',
			'wikipedia', 
			crud_ch('image', 'image1', crud::TOUT),
			crud_ch('desc', 'commentaire', crud::TOUT), 
			crud_ch('dep', 'Lieu_Dep', crud::TOUT ^ crud::RECURSIVE ), 
			crud_ch('lat', 'lat', crud::TOUT), 
			crud_ch('long', 'lng', crud::TOUT), 
		), 'Lieu' ); 
	}

	return $c; 
}

function ville_enr_grp($v)
{
	static $s=NULL, $i;

	if( is_null($s) )
	{
		$s = prereq('DELETE FROM Lieu_join WHERE id_lieu=?'); 
		$i = prereq('INSERT INTO Lieu_join(id_lieu, id_groupe) VALUES(?,?) '); 
	}

	if( $v->acc_id() )
	{
		exereq($s, array($v->acc_id() ) ); 
		foreach( $v->acc_tab_grp() as $idg )
		{
			exereq($i, array($v->acc_id(), $idg) ); 
		}
	}
}
