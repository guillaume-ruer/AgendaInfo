<?php

class ls_location
{
	public $page=0; 
	public $structure=0; 

	public $ch_nom_str = FALSE; 

	function requete() 
	{
		$where = $join = $select = ''; 

		if( !empty($this->structure) )
		{
			$where = 'WHERE e.structure='.absint($this->structure).' '; 
		}

		if( $this->ch_nom_str )
		{
			$join .= ' LEFT JOIN structure s ON s.id = e.structure '; 
			$select .= ', secuhtml::s.nom structure '; 
		}

		$loc = new reqa('
			SELECT absint::e.code, absint::e.id, secuhtml::e.template, secuhtml::e.nom,
				absint::e.nb_rss, absint::e.nb_ext, page_appelante::page_appelante
				'.$select.'
			FROM Externe e
			'.$join.'
			'.$where.'
			ORDER BY e.nb_ext DESC, e.nb_rss DESC, e.nom
		', NULL, $this->page, 20); 

		return $loc; 
	}
}

function page_appelante($c)
{
	if( empty($c) )
	{
		return array(); 
	}
	else
	{
		return (array)unserialize($c); 
	}
}
