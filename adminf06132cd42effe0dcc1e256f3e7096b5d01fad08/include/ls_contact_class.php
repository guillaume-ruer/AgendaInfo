<?php

class liste_contact
{
	public $autorise=FALSE;
	public $id_autorise=0; 
	public $compte_lie=FALSE; 

	function requete()
	{
		$auto = ''; 
		$compte = ''; 

		if($this->autorise )
		{
			$auto = '
			AND id IN( 
				SELECT Contact_id 
				FROM Utilisateurs u 
				LEFT OUTER JOIN autorise a 
					ON a.idu = u.id 
				WHERE id_autorise='.absint($this->id_autorise).'
				OR u.id='.absint($this->id_autorise).' 
			)
			';
		}

		if($this->compte_lie )
		{
			$compte = '
				LEFT JOIN Utilisateurs u 
					ON u.Contact_id = c.id 
			';
		}
		
		$contact = new reqa('
			SELECT secuhtml::adherent, absint::c.id, secuhtml::Lieu_Ville AS ville, secuhtml::type
			FROM Contact c
			LEFT OUTER JOIN Lieu 
				ON Lieu.Lieu_ID = c.Lieu
			'.$compte.' 
			WHERE adherent!=\'\' AND type NOT IN(\'\', \'non_adherent\' )  
			'.$auto.'
			ORDER BY TRIM(adherent) '  
		);

		return $contact; 
	}
}
