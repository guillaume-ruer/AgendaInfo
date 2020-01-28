<?php

class visuel_liste
{
	private $type=NULL;
	public $ordre=self::O_DATE;
	public $pagin=FALSE; 
	public $nb=10;
	public $page=0; 
	public $structure=NULL;
	public $ville=NULL;
	public $str_ville=NULL; 
	public $fi_date=self::D_ACTUEL; 
	public $droit=FALSE; // Ajoute un champs booléen indiquant si l'utilisateur actuel à le droit sur la structure ou non 
	private $conf=NULL; 

	// Ordre 
	const O_DATE = 0;
	const O_ALEAT = 1; 
	const O_ID = 2; 

	// Filtre de date 
	const D_ACTUEL = 0; // Aujourd'hui entre date début et date fin
	const D_FUTURE = 1; // A partir de date de début

	function __construct($conf, $type)
	{
		$this->conf = $conf; 
		$this->mut_type($type); 
	}

	function mut_type($type)
	{
		if( isset($this->conf[$type]) )
		{
			$this->type = $type; 
			return TRUE; 
		}
		else
		{
			return FALSE; 
		}
	}

	function acc_type()
	{
		return $this->type; 
	}

	function requete()
	{
		// Init 
		$ordre = $limit = $t = $where = $select = $join = ''; 

		
		// Type de visuel 
		$t = $this->conf[ $this->type ]['type']; 

		// Affichette actuel ou non
		if( $this->conf[ $this->type ]['date'] == TRUE )
		{
			if($this->fi_date == self::D_ACTUEL )
			{
				$where .= ' AND NOW() BETWEEN datedeb AND datefin ';
			}
			elseif( $this->fi_date == self::D_FUTURE )
			{
				$where .= ' AND NOW() <= datefin ';
			}
		}

		// Ordre 
		switch($this->ordre)
		{
			case self::O_DATE : 
				$ordre = ' ORDER BY datedeb '; 
			break; 
			case self::O_ALEAT : 
				$ordre = ' ORDER BY RAND() ';
			break; 
			default :
				$ordre = ' ORDER BY id DESC '; 
		}

		// Ville 
		if(!is_null($this->ville) )
		{
			switch($this->conf[ $this->type ]['ville'] )
			{
				case VISUEL_VILLE_LISTE : 
					$join .= '
					LEFT OUTER JOIN pub_ville p
						ON idpub = id
					LEFT OUTER JOIN visuel_grp_lieu vgl
						ON vgl.id_visuel = b.id 
					LEFT OUTER JOIN Lieu_join lj
						ON lj.id_groupe = vgl.id_grp_lieu
					'; 

					$where .= ' AND (p.idville = '.absint($this->ville).' OR 
						lj.id_lieu='.absint($this->ville).' ) '; 
				break;
				case VISUEL_VILLE_UNE :
					$where .= ' AND ville='.absint($this->ville).' '; 
				break; 
			}
		}

		if(!is_null($this->str_ville) )
		{
			$join .= '
				LEFT JOIN structure s 
					ON s.id = b.id_structure
			'; 

			$where .= ' AND s.ville='.absint($this->str_ville).' '; 
		}	

		// Nombre de visuel 
		$nb_par_page = 10;
		$p = NULL; 

		if( $this->pagin )
		{
			$nb_par_page = $this->nb;
			$p = $this->page; 
		}
		else
		{
			$limit = 'LIMIT '.absint($this->nb); 
		}


		//Filtrage par stucture 
		if( !is_null($this->structure) )
		{
			$where .= ' AND b.id_structure='.absint($this->structure);
		}

		// droit ou non sur la structure 
		if( $this->droit )
		{
			$select = ', str_droit::b.id_structure droit '; 
		}

		// Contact 

		if( $this->conf[ $this->type ]['contact'] == TRUE )
		{
			$join .= '
				LEFT JOIN structure_contact sc
					ON sc.id= b.contact
			'; 
			$select .= ', b.contact id_contact, secuhtml::sc.titre c_titre, secuhtml::sc.tel c_tel, secuhtml::sc.site c_site';
		}
		
		// Requete finale  
		$req = new reqa('
			SELECT absint::b.id, secuhtml::Image img, secuhtml::DateDeb datedeb, 
				secuhtml::DateFin datefin, Texte texte, secuhtml::b.Titre titre,
				secuhtml::URL url, absint::Clics clic, absint::Affichages aff
				'.$select.' 
			FROM Bandeaux b
			'.$join.'
			WHERE b.type LIKE(\''.$t.'\') 
			'.$where.'
			'.$ordre.'
			'.$limit.'
		', NULL, $p, $nb_par_page ); 

		return $req; 
	}
}

