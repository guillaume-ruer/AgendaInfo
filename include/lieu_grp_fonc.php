<?php

function lieu_grp_init($id)
{
	$lg=new lieu_grp; 

	$donne = req('SELECT id, Nom nom, ordre, num FROM Lieu_grp WHERE id='.(int)$id.' LIMIT 1 '); 

	if( $do = fetch($donne) )
	{
		$lg->hydrate( $do ); 

		$donne = req('SELECT id_lieu FROM Lieu_join WHERE id_groupe='.(int)$lg->acc_id().' '); 
		
		while( $do = fetch($donne) )
		{
			$lg->add_lieu($do['id_lieu']); 
		}
	}

	return $lg; 
}

function lieu_grp_crud()
{
	static $c=NULL; 

	if( is_null($c) )
	{
		$c = new crud( array(
			crud_ch('nom', 'Nom', crud::TOUT), 
			'ordre', 
			'num', 
		), 'Lieu_grp' ); 
	}

	return $c; 
}

function lieu_grp_enr_lieu($grp)
{
	static $s=NULL, $i;

	if( is_null($s) )
	{
		$s = prereq('DELETE FROM Lieu_join WHERE id_groupe=?'); 
		$i = prereq('INSERT INTO Lieu_join(id_lieu, id_groupe) VALUES(?,?) '); 
	}

	if( $grp->acc_id() )
	{
		exereq($s, array($grp->acc_id() ) ); 
		$tab_value = []; 

		foreach( $grp->acc_tab_lieu() as $idl )
		{
			$tab_value[] = '('.$idl.','.$grp->acc_id().')'; 
		}

		if( !empty($tab_value) )
		{
			req('INSERT INTO Lieu_join(id_lieu, id_groupe) VALUES'.implode(',', $tab_value).' '); 
		}
	}
}
