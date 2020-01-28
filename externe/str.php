<?php
include '../include/init.php'; 
require C_INC.'fonc_cache.php'; 
require C_INC.'structure_fonc.php'; 

header('content-type: application/json ');

if( !is_null($code= (isset($_GET['code']) ) ? noui($_GET['code']) : NULL ) )
{
	$id = cache_id($code); 

	if( cache($id, 1800) )
	{
		$donne = req('
			SELECT s.nom, s.id
			FROM Externe e
			JOIN externe_str es
				ON e.id = es.id_externe
			JOIN structure s
				ON es.id_str = s.id 
			WHERE e.code='.$code.' 
		');

		$tab = array(); 

		while( $do = fetch($donne) )
		{
			$tab[] = $do; 
		}

		echo json_encode( $tab ); 
	}
	cache(); 
}
elseif( !is_null($ids = (isset($_GET['ids']) ) ? noui($_GET['ids']) : NULL ) )
{
	$id = cache_id($ids); 

	if( cache($id, 1800) ) 
	{ 

		$donne = req('
			SELECT s.id, s.nom, s.logo, s.adresse adresse__rue, l.Lieu_Ville AS adresse__ville__nom, 
				s.ville adresse__ville__id_ville, l.Lieu_CP adresse__ville__cp, 
				s.presentation description, l.Lieu_Dep adresse__ville__dep, 
				banniere, banniere_url
			FROM structure AS s
			LEFT JOIN Lieu AS l
				ON l.Lieu_ID = s.ville
			WHERE s.id = '.$ids.' 
			LIMIT 1 
		');

		if( $do = fetch($donne) )
		{
			$str = genere_init($do); 

			$donne = req('SELECT titre, id, tel, site FROM structure_contact WHERE id_structure='.(int)$str['id'] );  
			$str['contact'] = array(); 
	     
			while($do = fetch($donne) )
			{   
				$str['contact'][] = $do;  
			}   

			echo json_encode($str); 
		}
	}
	cache(); 
}
