<?php
require_once C_INC.'evenement_class.php'; 
require_once C_INC.'historique_class.php'; 

// Constante de champs 
$tab_champ = array('DESC', 'CAT', 'CAT_GROUPE', 'NB_DATE', 'CONTACT', 'ETAT', 'LIEU', 'DATE', 'TOUTE_DATE',
       'CREATEUR', 'DATE_CREATION', 'DER_DATE_MODIF', 'DER_MODIFIEUR', 'TARIF', 'AFFICHE' ); 
$i=1; 
foreach( $tab_champ as $cte )
{
       define('EVCH_'.$cte, $i ); 
       $i<<=1; 
}

class ls_evenement extends reqo
{
	/*
		Quel champs récupéré ? 
	*/

	private $champ = EVCH_DATE; 

	/*
		Comment filtré ? 
	*/

	private $fi_modifieur = NULL; 
	private $fi_str_actif = TRUE; 
	private $fi_date = TRUE;
	private $fi_date_min = NULL; // à partir de quand ? 
	private $fi_date_max = NULL; // jusqu'à quand ? 
	private $fi_lieu = NULL; // par lieu  
	private $fi_grp_lieu = NULL; // par groupe de lieu 
	private $fi_theme = NULL; // par thème 
	private $fi_id_externe = NULL; // par id externe 
	private $fi_structure= NULL;  // Evenemnt d'une structure
	private $fi_structure_droit = NULL;  // Droit structure
	private $fi_grpstr = NULL;  // Droit structure
	private $fi_createur = NULL; // par créateur 
	private $fi_lei = self::LEI_TOUT; // événement du lei 
	private $fi_actif = evenement::ACTIF; 
	private $fi_recherche = NULL; 
	private $fi_date_modif_min = NULL; 
	private $fi_date_creat_min = NULL; 
	private $fi_type=NULL; 
	private $fi_ignore=NULL; 
	private $fi_event_id=NULL; 
	private $fi_rayon=NULL; 
	private $fi_planning_stat=FALSE; 

	/*
		spécial 
	*/

	private $switch = FALSE;  
	private $order = self::ORDER_DATE_ALEAT; 

	/*
		Import/Lei
	*/
	const LEI_TOUT = 0; 
	const LEI_SEUL = 1; 
	const LEI_SANS = 2;
	const STQ_SEUL = 3; 

	/*
		Etat 
	*/

	const TOUT = -1;
	const ACTIF_INACTIF = -2; 

	/*
		Order 
	*/

	const ORDER_DATE_ALEAT = 0; 
	const ORDER_MES10DER = 1; 
	const ORDER_DER_MODIF = 2; 
	const ORDER_DATE_CREATION = 3; 
	const ORDER_RAND = 4; 
	const ORDER_DATE_LIEU = 5; 

	function __construct( $do=array() )
	{
		parent::__construct(); 
		$this->fi_date_min = date('Y-m-d' ); 
		$this->fi_date_max = date('Y-m-d', time() + 120*24*3600 );
		$this->mut_sorti('evenement'); 
		$this->hydrate($do); 

	}

	/*
		Accesseurs 
	*/

	function acc_champ(){ return $this->champ; } 

	/*
		Mutateurs
	*/

	function mut_champ($ch){ $this->champ = $ch; } 
	function ajt_champ($ch){ $this->champ |=$ch; } 

	function mut_fi_modifieur($id) { $this->fi_modifieur=noui($id); } 
	function mut_fi_str_actif($b){ $this->fi_str_actif=(bool)$b; } 
	function mut_fi_date($b){ $this->fi_date = (bool)$b; } 
	function mut_fi_lieu($id) { $this->fi_lieu = noui($id); }
	function mut_fi_rayon($r) { $this->fi_rayon = noui($r); }
	function mut_fi_grp_lieu($id) { $this->fi_grp_lieu = noui($id); } 
	function mut_fi_planning_stat($b){ $this->fi_planning_stat=(bool)$b; } 

	function mut_fi_theme($id)
	{ 
		if( is_array($id) )
		{
			$this->fi_theme = array_map('intval', $id); 
		}
		else
		{
			$f = noui($id); 
			$this->fi_theme = is_null($f) ? NULL : [$f]; 
		}
	}

	function mut_fi_id_externe($id){ $this->fi_id_externe = noui($id); }
	function mut_fi_structure($b){ $this->fi_structure = noui($b); }
	function mut_fi_grpstr($gs){ $this->fi_grpstr = noui($gs); }
	function mut_fi_structure_droit($b){ $this->fi_structure_droit = noui($b); } //// !!!! 
	function mut_fi_createur($id){ $this->fi_createur = noui($id); } 
	function mut_order($o)
	{
		if( in_array($o, array(self::ORDER_DATE_ALEAT, self::ORDER_MES10DER, 
			self::ORDER_DER_MODIF, self::ORDER_DATE_CREATION, self::ORDER_RAND, self::ORDER_DATE_LIEU ) ) )
		{
			$this->order = $o; 
		}
	}

	function mut_fi_actif($a)
	{ 
		if( in_array($a, array(evenement::MASQUE, evenement::ACTIF, evenement::SUPP, self::TOUT, self::ACTIF_INACTIF ) ) )
		{
			$this->fi_actif = $a;
		}
	} 

	function mut_fi_lei($m)
	{ 
		$this->fi_lei = $m; 
	} 

	function mut_fi_date_max($dm)
	{
		$this->fi_date_max = $dm; 
	}

	function mut_fi_date_min($dm) 
	{
		$this->fi_date_min =$dm; 
	}

	function mut_fi_recherche($fr){ $this->fi_recherche = empty($fr) ? NULL : (string)$fr; }

	function mut_fi_date_modif_min($dmm)
	{
		if( is_numeric($dmm) )
		{
			$this->fi_date_modif_min = (int)$dmm; 
		}
		else
		{
			return FALSE; 
		}
	}	

	function mut_fi_date_creat_min($dcm)
	{
		if( is_numeric($dcm) )
		{
			$this->fi_date_creat_min = (int)$dcm; 
		}
		else
		{
			return FALSE; 
		}
	}

	function mut_fi_type($fi_type){ $this->fi_type=is_numeric($fi_type) ? $fi_type : NULL; }

	function mut_fi_ignore($fi_ignore)
	{ 
		$this->fi_ignore = (is_array($fi_ignore) && !empty($fi_ignore) ) ? array_map('intval', $fi_ignore) : NULL; 
	}

	function mut_fi_event_id($fi_event_id)
	{ 
		$this->fi_event_id = (is_array($fi_event_id) && !empty($fi_event_id) ) ? array_map('intval', $fi_event_id) : NULL; 
	}

	/*
		Méthodes 
	*/

	function ch_actif($ch) { return (bool)( $this->champ & $ch );  }

	function requete($null=NULL)
	{
		/*
			On complète la requête pour obtenir les champs désiré. 
		*/
		//init 
		$tab_regle = array(); 
		$join = $select = $where = ''; 

		// Description, nombre de date 
		$select .= $this->ch_actif(EVCH_NB_DATE) ? ', COUNT(DISTINCT ed.ED_id) AS nb_date' : ''; 
		$select .= $this->ch_actif(EVCH_DESC)? ', det.Description AS `desc`' : ''; 
		$select .= $this->ch_actif(EVCH_DATE_CREATION) ? ', e.Creat_datetime AS date_creation ' : ''; 
		$select .= $this->ch_actif(EVCH_AFFICHE) ? ', e.affiche, e.affiche_url ' : ''; 

		/*
			Date 
		*/ 


		if( $this->fi_date )
		{
			if( !is_null($this->fi_date_min) && !is_null($this->fi_date_max) )
			{
				
				$where .= "AND ( ed.Evenement_Date BETWEEN '$this->fi_date_min' AND '$this->fi_date_max' )\n"; 
			}
			elseif( !is_null($this->fi_date_min) )
			{
				$where .= "AND ed.Evenement_Date >= '$this->fi_date_min'\n"; 
			}
			elseif( !is_null($this->fi_date_max) )
			{
				$where .= "AND ed.Evenement_Date <= '$this->fi_date_max'\n"; 
			}
		}

		if( $this->fi_date OR $this->ch_actif(EVCH_DATE) OR $this->ch_actif(EVCH_TOUTE_DATE) )
		{
			$join .= " 
				LEFT OUTER JOIN Evenement_dates AS ed\n\tON ed.Evenement_id = e.id\n
				LEFT OUTER JOIN Evenement_dates AS eds\n\tON eds.Evenement_id = e.id\n
			" ;

			$where .= ' AND eds.Evenement_Date >= \''.date('Y-m-d').'\' '; 
		}

		if( $this->ch_actif(EVCH_TOUTE_DATE) )
		{
			$select .= ', GROUP_CONCAT( DISTINCT eds.Evenement_Date ORDER BY eds.Evenement_Date ) tab_date'; 
		}
		elseif( $this->ch_actif(EVCH_DATE) ) 
		{
			$select .= ', MIN(ed.Evenement_Date) AS tab_date ';
		}


		/*
			Filtrage par id externe 
		*/
		$filtre = 0; 
		if( !is_null($this->fi_id_externe) )
		{
			$ide = $this->fi_id_externe; 

			$donne = req('SELECT filtre FROM Externe WHERE id='.$ide.' LIMIT 1 '); 
			$do = fetch($donne); 
			$filtre = absint($do['filtre']);

			/*
				Lieux / groupe de lieux 
			*/


			if( $filtre & LOC_LIEUX )
			{
				$where .= ' AND el.Lieu_id IN ( SELECT id_lieu FROM Externe_lieux WHERE id_externe='.$ide.') ';
			}

			if( $filtre & LOC_GRPLIEUX )
			{
				$where .= ' AND lj.id_groupe IN ( SELECT eg.id_groupe FROM externe_grplieu eg WHERE eg.id_externe='.$ide.' ) '; 
			}

			/*
				Thème 
			*/

			if( $filtre & LOC_THEME )
			{
				$where .= ' AND c.groupe IN ( SELECT id_theme FROM externe_theme WHERE id_externe='.$ide.' ) '; 
			}

			/*
				Contact / structure 
			*/

			if( $filtre & LOC_CONTACT )
			{
				$where .= ' AND e.Contact_id IN( SELECT id_contact FROM Externe_contact WHERE id_externe= '.$ide.' )'; 
			}

			if( $filtre & LOC_STR )
			{
				$where .= ' AND co.id_structure IN( SELECT id_str FROM externe_str WHERE id_externe='.$ide.' )'; 
			}
		}

		/*
			Etat 
		*/

		$select .= $this->ch_actif(EVCH_ETAT) ? ', e.Actif AS etat ' : ''; 

		/*
			Catégorie 
		*/
		if( $this->ch_actif(EVCH_CAT) )
		{ 
			$select .= ', c.CAT_IMG AS categorie__img, 
				c.CAT_ID categorie__id, c.CAT_NAME_FR categorie__nom, c.groupe categorie__groupe,
				c.width categorie__width, c.height categorie__height
			 '; 
		}

		if( !is_null($this->fi_theme) )
		{
			$where .= ' AND c.groupe IN('.implode(',', $this->fi_theme).")\n" ;
		}
			
		if($this->ch_actif(EVCH_CAT) OR !is_null($this->fi_theme) || ($filtre & LOC_THEME ) )
		{
			$join .= "LEFT OUTER JOIN Categories AS c\n\tON c.CAT_ID = e.Cat_id\n"; 
		}

		if( $this->ch_actif(EVCH_CAT_GROUPE) )
		{
			$select .= ', cg.nom_fr AS categorie__groupe_nom'; 
			$join .= "LEFT OUTER JOIN categories_grp AS cg\n\tON c.groupe = cg.id\n"; 
		}


		/*
			tarif 
		*/
		$select .= $this->ch_actif(EVCH_TARIF) ? ', e.tarif tarif__id' : ''; 


		/*
			Ville 
		*/

		if($this->ch_actif(EVCH_LIEU) )
		{
			$select .= ', GROUP_CONCAT(DISTINCT lch.Lieu_id, \':\', lch.Lieu_Ville, \':\', lch.Lieu_Dep, \':\', lch.lat, \':\', lch.lng SEPARATOR \';\' ) AS tab_lieu'; 
		}

		if( !is_null($this->fi_lieu) )
		{
			if( !is_null($this->fi_rayon) )
			{
				$ville = req('SELECT * FROM Lieu WHERE Lieu_id = '.(int)$this->fi_lieu.' '); 
				
				if( $do = fetch($ville) )
				{
					$lat_ville = str_replace(',','.', deg2rad( (float)$do['lat'] ) );
					$long_ville = str_replace(',', '.', deg2rad( (float)$do['lng']) );

					$diametre_terre = 6371; 

					$where .= "
						AND ( $diametre_terre * SQRT( 
							POW( ( $long_ville - RADIANS(l.lng) )*COS( ( RADIANS(l.lat)+$lat_ville )/2 ), 2 ) + 
							POW( $lat_ville - RADIANS(l.lat),2)
						) 
						)<= {$this->fi_rayon}
					";
				}
			}
			else
			{
				$where .=' AND el.Lieu_id='. (int)$this->fi_lieu."\n"; 
			}
		}
		elseif( !is_null($this->fi_grp_lieu) )
		{
			$where .=' AND lj.id_groupe='.(int)$this->fi_grp_lieu."\n";	
		}

		if( !is_null($this->fi_grp_lieu) OR !is_null($this->fi_lieu) OR $this->ch_actif(EVCH_LIEU) || ($filtre & (LOC_GRPLIEUX | LOC_LIEUX ) ) )
		{
			$join .= "LEFT OUTER JOIN Evenement_lieux AS el\n\tON el.Evenement_id = e.id\n"; 
		}

		if( !is_null($this->fi_lieu) && !is_null($this->fi_rayon) )
		{
			$join .= "LEFT OUTER JOIN Lieu AS l\n\tON l.Lieu_ID = el.Lieu_id\n"; 
		}

		
		if($this->ch_actif(EVCH_LIEU) ) 
		{
			$join .= "LEFT OUTER JOIN Evenement_lieux AS elch\n\tON elch.Evenement_id = e.id\n"; 
			$join .= "LEFT OUTER JOIN Lieu AS lch\n\tON elch.Lieu_id = lch.Lieu_ID\n"; 
		}

		if( !is_null($this->fi_grp_lieu) || ($filtre & LOC_GRPLIEUX) )
		{
			$join .= "LEFT OUTER JOIN Lieu_join AS lj\n\tON lj.id_lieu = el.Lieu_ID\n";

		}

		/*
			Filtrage dans l'admin  
		*/
		
		// LEI 
		switch( $this->fi_lei )
		{
			case self::LEI_SEUL : $where .= "AND e.source=".evenement::LEI."\n"; break;
			case self::STQ_SEUL : $where .= "AND e.source=".evenement::STQ."\n"; break;
			case self::LEI_SANS : $where .= "AND e.source=".evenement::IL."\n"; break; 
		}

		//Actif
		if( $this->fi_actif == self::ACTIF_INACTIF)
		{
			$where .= "AND e.Actif IN(".evenement::ACTIF.",".evenement::MASQUE.")\n"; 
		}
		else
		{
			$where .= ($this->fi_actif != self::TOUT ) ? 'AND e.Actif = '. (int)$this->fi_actif."\n" : '' ; 
		}

		/*
			structure 
		*/

		if($this->ch_actif(EVCH_CONTACT) || $this->fi_str_actif || ($filtre & LOC_STR ) || !is_null($this->fi_grpstr) )
		{
			$join .='
			LEFT OUTER JOIN structure_contact co
				ON co.id = e.Contact_id
			LEFT OUTER JOIN structure s 
				ON s.id = co.id_structure
			'; 
		}

		if($this->ch_actif(EVCH_CONTACT) )
		{
			$select .=', co.id contact__id,
				s.nom contact__structure__nom, 
				s.code_externe contact__structure__code_externe, 
				co.titre contact__titre, 
				co.site contact__site, co.tel contact__tel, 
				s.id AS contact__structure__id,  
				s.logo contact__structure__logo,
				s.actif contact__structure__actif';	
		}

		if( $this->fi_str_actif )
		{
			$where .= ' AND s.actif=1 '; 
		}

		if($this->fi_structure_droit) 
		{

			$donne = req('SELECT id_structure FROM Utilisateurs WHERE id='.$this->fi_structure_droit.' LIMIT 1 ');
			$ids = ($do = fetch($donne) )? absint($do['id_structure']) : 0; 

			$where .= '
			AND ( Contact_id IN (
				SELECT sc.id 
				FROM structure_contact sc 
				LEFT JOIN structure_droit sd
					ON sc.id_structure = sd.structure
				WHERE sd.utilisateur = '.$this->fi_structure_droit.'
				AND sd.droit & '.STR_EVENEMENT.'
			)
			OR Contact_id IN( SELECT sc.id FROM structure_contact sc WHERE sc.id_structure='.$ids.' )
			)
			';

		}

		if($this->fi_structure) 
		{
			if( $this->fi_structure == - 1 )
			{
				$where .= ' AND Contact_id IS NULL ';
			}
			else
			{
				$where .= '
				AND Contact_id IN (
					SELECT sc.id 
					FROM structure_contact sc 
					WHERE id_structure = '.$this->fi_structure.'
				)
				';
			}
		}

		if( !is_null($this->fi_grpstr) )
		{
			$join .= ' JOIN structure_grp_structure sgs ON sgs.id_structure=s.id '; 
			$where .= ' AND sgs.id_structure_grp='.$this->fi_grpstr.' ';
		}

		/*
			Créateur 
		*/

		if( !is_null($this->fi_date_creat_min) )
		{
			$where .= ' AND e.Creat_datetime >= \''. date('Y-m-d', $this->fi_date_creat_min).'\' '; 
		}
		
		if( !is_null($this->fi_createur) )
		{
			$where .=' AND e.Creat_id='.$this->fi_createur ; 
		}

		if($this->ch_actif(EVCH_CREATEUR) )
		{
			$select .= ', e.Creat_id AS createur__id, u1.User AS createur__nom'; 
			$join .= "RIGHT JOIN Utilisateurs AS u1\n\tON u1.ID = e.Creat_id\n"; 
		}

		/*
			Modifieur 
		*/

		if( !is_null( $this->fi_date_modif_min ) )
		{
			$where .=' AND h.date > '.(int)$this->fi_date_modif_min.' '; 
		}

		if( ($this->order == self::ORDER_DER_MODIF ) || $this->ch_actif(EVCH_DER_MODIFIEUR) || $this->ch_actif(EVCH_DER_DATE_MODIF) 
			|| !is_null($this->fi_date_modif_min) )	
		{
			$join .= "LEFT JOIN historique h\n\tON h.id=e.der_historique AND h.type=2\n"; 
		}

		if($this->ch_actif(EVCH_DER_DATE_MODIF) )
		{
			$select .= ", h.date date_modif "; 
		}

		if( $this->ch_actif(EVCH_DER_MODIFIEUR) ) 
		{
			$select .= ", um.User modifieur__nom, um.id modifieur__id"; 
			$join .= "LEFT JOIN Utilisateurs um\n\tON h.idutr=um.id\n";
		}

		if( !is_null($this->fi_modifieur) )
		{
			$join .= "LEFT JOIN historique h2\n\tON h2.idevent=e.id\n"; 
			$where .= ' AND h2.idutr='.$this->fi_modifieur.' AND h2.type='.historique::MODIF.' '; 

		}

		if( !is_null($this->fi_type) )
		{
			$where .=' AND e.type='.$this->fi_type.' '; 
		}

		if( !is_null($this->fi_ignore) )
		{
			$where .= ' AND e.id NOT IN('.implode(',', $this->fi_ignore).') '; 
		}

		if( !is_null($this->fi_event_id) )
		{
			$where .= ' AND e.id IN('.implode(',', $this->fi_event_id).') '; 
		}


		/*
			Ordonner
		*/
	
		$order = ''; 

		switch( $this->order )
		{
			case self::ORDER_DATE_ALEAT; 
				if( $this->ch_actif(EVCH_DATE) )
				{
					$order = 'ORDER BY MIN(ed.Evenement_Date), e.Aleat'; 
				}
			break; 
			case self::ORDER_MES10DER; 
				$order = 'ORDER BY e.Creat_datetime DESC, e.Creat_id '; 
			break; 
			case self::ORDER_DER_MODIF;
				$order = 'ORDER BY h.date DESC '; 
			break; 
			case self::ORDER_DATE_CREATION;
				$order = 'ORDER BY e.Creat_datetime DESC'; 
			break; 
			case self::ORDER_RAND : 
				$order = 'ORDER BY RAND()'; 
			break; 
			case self::ORDER_DATE_LIEU : 

				if( $this->ch_actif(EVCH_LIEU) )
				{
					$order = 'ORDER BY MIN(ed.Evenement_Date), lch.Lieu_id'; 
				}
			break; 
		}

		/*
			Stat planning
		*/

		if( $this->fi_planning_stat)
		{
			$join .= ' JOIN planning_stat_event pse ON pse.id_event = e.id '; 
			$select .= ', COUNT(DISTINCT pse.id_planning_stat) nbp'; 
			$order = ' ORDER BY nbp DESC';	
		}

		/*
			Recherche 
		*/

		if( !is_null($this->fi_recherche) )
		{
			$where .= ' AND ( det.Titre LIKE( \'%'.secubdd($this->fi_recherche).'%\' ) 
				OR det.Description LIKE(\'%'.secubdd($this->fi_recherche).'%\') ) '; 
		}	

		/*
			Construction de la requête presque finale. 
		*/
		$sql  = '
			SELECT e.id, e.image, e.source, e.date_maj, e.lei id_externe, e.public public__id, det.Titre AS titre'.$select.'
			FROM Evenement  AS e
			LEFT OUTER JOIN Evenement_details AS det
				ON det.Evenement_id = e.id AND det.Langue_id = 1
			'.$join.'
			WHERE 1
			'.$where.'
			GROUP BY e.id
			'.$order.'
		'; 
		
		parent::requete($sql); 
	}

	function nb_par_date()
	{
		$join = $where = ''; 

		if( !empty($this->fi_theme) )
		{
			$join .= ' JOIN Categories c ON c.CAT_ID = e.Cat_id '; 
			$where .= ' AND c.groupe IN('.implode(',', $this->fi_theme).') '; 
		}

		if( !empty($this->fi_ignore) )
		{
			$where .= ' AND e.id NOT IN('.implode(',', $this->fi_ignore).') '; 
		}

		if( !empty($this->fi_lieu) )
		{
			$join .= ' JOIN Evenement_lieux el ON el.Evenement_id = e.id '; 

			if( !empty($this->fi_rayon) )
			{
				$ville = req('SELECT * FROM Lieu WHERE Lieu_id = '.(int)$this->fi_lieu.' '); 
				
				if( $do = fetch($ville) )
				{
					$join .= ' JOIN Lieu l ON l.Lieu_ID = el.Lieu_id ';
					$lat_ville = str_replace(',','.', deg2rad( (float)$do['lat'] ) );
					$long_ville = str_replace(',', '.', deg2rad( (float)$do['lng']) );

					$diametre_terre = 6371; 

					$where .= "
						AND ( $diametre_terre * SQRT( 
							POW( ( $long_ville - RADIANS(l.lng) )*COS( ( RADIANS(l.lat)+$lat_ville )/2 ), 2 ) + 
							POW( $lat_ville - RADIANS(l.lat),2)
						) 
						)<= {$this->fi_rayon}
					";
				}
			}
			else
			{
				$where .= ' AND el.Lieu_id = '.$this->fi_lieu.' '; 
			}

		}

		$donne = req('
			SELECT COUNT( DISTINCT(e.id) ) nb, Evenement_date date
			FROM Evenement e
			JOIN Evenement_dates ed
				ON e.id = ed.Evenement_id
			'.$join.'
			WHERE Evenement_date BETWEEN "'.date('Y-m-d').'" AND "'.date('Y-m-d', mktime(0,0,0,date('n')+4) ).'"
			AND Actif=1
			'.$where.'
			GROUP BY Evenement_date
		');

		$tab_date = []; 

		while($do = fetch($donne) )
		{
			$tab_date[ $do['date'] ] = $do['nb'];
		}	
		
		return $tab_date; 
	}

	function nb_par_lieu()
	{
		$join = $where = ''; 

		if( !empty($this->fi_theme) )
		{
			$join .= ' JOIN Categories c ON c.CAT_ID = e.Cat_id '; 
			$where .= ' AND c.groupe IN('.implode(',', $this->fi_theme).') '; 
		}

		if( !empty($this->fi_ignore) )
		{
			$where .= ' AND e.id NOT IN('.implode(',', $this->fi_ignore).') '; 
		}

		if( !empty($this->fi_lieu) )
		{
			if( !empty($this->fi_rayon) )
			{
				$ville = req('SELECT * FROM Lieu WHERE Lieu_id = '.(int)$this->fi_lieu.' '); 
				
				if( $do = fetch($ville) )
				{
					$lat_ville = str_replace(',','.', deg2rad( (float)$do['lat'] ) );
					$long_ville = str_replace(',', '.', deg2rad( (float)$do['lng']) );

					$diametre_terre = 6371; 

					$join .= " JOIN Lieu lf ON lf.Lieu_ID = el.Lieu_id\n"; 
					$where .= "
						AND ( $diametre_terre * SQRT( 
							POW( ( $long_ville - RADIANS(lf.lng) )*COS( ( RADIANS(lf.lat)+$lat_ville )/2 ), 2 ) + 
							POW( $lat_ville - RADIANS(lf.lat),2)
						) 
						)<= {$this->fi_rayon}
					";
				}
			}
			else
			{
				$where .= ' AND el.Lieu_id = '.$this->fi_lieu.' '; 
			}
		}

		if( $this->fi_date )
		{
			if( !is_null($this->fi_date_min) && !is_null($this->fi_date_max) )
			{
				$where .= "AND ( ed.Evenement_Date BETWEEN '$this->fi_date_min' AND '$this->fi_date_max' )\n"; 
			}
			elseif( !is_null($this->fi_date_min) )
			{
				$where .= "AND ed.Evenement_Date >= '$this->fi_date_min'\n"; 
			}
			elseif( !is_null($this->fi_date_max) )
			{
				$where .= "AND ed.Evenement_Date <= '$this->fi_date_max'\n"; 
			}
		}
		else
		{
			$where .= 'Evenement_date BETWEEN "'.date('Y-m-d').'" AND "'.date('Y-m-d', mktime(0,0,0,date('n')+4) ).'"'; 
		}


		$donne = req('
			SELECT l.Lieu_ID id, COUNT( DISTINCT(e.id) ) nbe, l.Lieu_Ville value, l.lat, l.`lng` `long`, l.Lieu_Dep dep
			FROM Evenement e
			JOIN Evenement_dates ed
				ON e.id = ed.Evenement_id
			JOIN Evenement_lieux el ON el.Evenement_id = e.id
			JOIN Evenement_lieux els ON els.Evenement_id = e.id
			JOIN Lieu l ON l.Lieu_ID = els.Lieu_id
			'.$join.'
			WHERE Actif=1
			'.$where.'
			GROUP BY els.Lieu_ID
		');

		$tab_lieu = []; 

		while($do = fetch($donne) )
		{
			$tab_lieu[ (int)$do['id'] ] = array(
				'id' => (int)$do['id'], 
				'nbe' => (int)$do['nbe'], 
				'value' => secuhtml($do['value']).' ('.$do['dep'].')', 
				'lat' => (float)$do['lat'],
				'long' => (float)$do['long']
			);
		}	
		
		return $tab_lieu; 
	}
}
