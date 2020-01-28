<?php

class liste_contact
{
	public $fi_structure=0; 
	public $fi_contact_structure=0; 

	function set_fi_structure($fs)
	{
		$this->fi_structure=absint($fs); 
	}

	function requete()
	{
		$where = ''; 

		if(!empty($this->fi_structure) )
		{
			$donne = req('SELECT id_structure FROM Utilisateurs WHERE id='.$this->fi_structure.' LIMIT 1 ');
			$ids = ( $do= fetch($donne) ) ? absint($do['id_structure']) : 0 ; 

			$where .= ' AND ( 
					sc.id IN(
						SELECT sc.id 
						FROM structure_contact sc 
						LEFT JOIN structure_droit sd
							ON sc.id_structure = sd.structure
						WHERE (sd.utilisateur = '.$this->fi_structure.'
						AND sd.droit & '.STR_EVENEMENT.')
					) 
					OR sc.id IN( SELECT sc.id FROM structure_contact sc WHERE sc.id_structure='.$ids.' )
				)
			';
		}

		if( !empty( $this->fi_contact_structure ) ) 
		{
			$where = ' AND s.id='.$this->fi_contact_structure.' ' ; 

		}

		$contact = new reqa('
			SELECT secuhtml::sc.titre, absint::sc.id, secuhtml::s.nom, secuhtml::Lieu_Ville ville, 
				secuhtml::sc.tel, secuhtml::sc.site 
			FROM structure s
			LEFT JOIN structure_contact sc 
				ON sc.id_structure = s.id
			LEFT JOIN Lieu l
				ON s.ville = l.Lieu_ID 
			WHERE s.nom!=\'\' AND s.type NOT IN(\'\', \'non_adherent\' ) AND sc.id IS NOT NULL 
			'.$where.' 
			ORDER BY TRIM(s.nom), TRIM(titre)
		');

		return $contact; 
	}
}
