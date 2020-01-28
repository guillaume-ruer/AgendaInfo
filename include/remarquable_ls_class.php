<?php

class remarquable_ls extends reqo
{
	protected $fi_type = NULL; 
	protected $fi_lieu = NULL; 
	protected $fi_rayon = NULL; 

	function mut_fi_type($fi_type)
	{
		if( is_null($fi_type) )
		{
			$this->fi_type = NULL; 
		}
		elseif( is_array($fi_type) )
		{
			$this->fi_type = array_map('intval', $fi_type); 
		}
	}

	function mut_fi_lieu($lieu){ $this->fi_lieu = is_null($lieu) ? NULL : (int)$lieu; }
	function mut_fi_rayon($rayon){ $this->fi_rayon = is_null($rayon) ? NULL : (int)$rayon; }

	private function filtre()
	{
		$where = ''; 

		if( !is_null($this->fi_type) )
		{
			$where .= ' AND r.type IN('.implode(',', $this->fi_type).') '; 
		}

		if( !is_null($this->fi_lieu) )
		{
			$ville = req('SELECT * FROM Lieu WHERE Lieu_id = '.(int)$this->fi_lieu.' '); 
			
			if( $do = fetch($ville) )
			{
				$lat_ville = str_replace(',','.', deg2rad( (float)$do['lat'] ) );
				$long_ville = str_replace(',', '.', deg2rad( (float)$do['lng']) );

				$diametre_terre = 6371; 

				$rayon = !is_null($this->fi_rayon) ? ($this->fi_rayon) : 2; 
				$idl = (int)$this->fi_lieu; 

				$where .= "
					AND 
					(
						( $diametre_terre * SQRT( 
							POW( ( $long_ville - RADIANS(r.lng) )*COS( ( RADIANS(r.lat)+$lat_ville )/2 ), 2 ) + 
							POW( $lat_ville - RADIANS(r.lat),2)
							) 
							<= $rayon
						)
						OR ville = $idl
					)
				";
			}
		}
		
		return $where; 
	}

	function requete($null = NULL)
	{
		$this->mut_sorti('remarquable'); 

		$where = $this->filtre(); 

		$sql = '
			SELECT r.id, r.titre, r.texte `desc`, r.ville ville__id, r.lat, r.lng `long`,
				l.Lieu_Ville ville__nom, r.type, r.site, r.tel, r.mail
			FROM remarquable r
			LEFT OUTER JOIN Lieu l
				ON l.Lieu_ID = r.ville 
			WHERE 1 
			'.$where.'
		';

		parent::requete($sql); 
	}

	function tout_rem()
	{
		$where = $this->filtre(); 

		$sql = '
			SELECT r.id, r.titre, r.texte `desc`, r.lat, r.lng `long`, r.type, l.Lieu_Ville ville_nom
			FROM remarquable r
			LEFT OUTER JOIN Lieu l
				ON r.ville = l.Lieu_ID
			WHERE 1 
			'.$where.'
		';

		$donne = req($sql); 	

		$tab_rem = []; 

		while($do = fetch($donne) )
		{
			$do['type_html'] = remarquable::type_html($do['type'], 30, 30); 

			if( $do['type'] == remarquable::MUSEE )
			{
				$do['titre'] = $do['titre'].' - '.$do['ville_nom']; 
			}

			$tab_rem[ $do['id'] ] = $do; 
		}

		return $tab_rem; 
	}
}
