<?php
require_once C_INC.'structure_class.php'; 

class ls_structure extends reqo 
{
	private $fi_droit=TRUE;
	private $fi_recherche=NULL; 
	private $fi_ville = 0; 

	function __construct($do=array() )
	{
		parent::__construct($do);
		$this->mut_sorti('structure'); 
	}

	function mut_fi_droit($b){ $this->fi_droit = (bool)$b; }
	function mut_fi_recherche($fr){ $this->fi_recherche = empty($fr) ? NULL : (string)$fr; }
	function mut_fi_ville($v){ $this->fi_ville = absint($v); }

	function requete($sql=NULL)
	{
		global $MEMBRE; 

		$join = ''; 
		$select =''; 
		$where = ''; 

		if( $this->fi_droit )
		{
			$select .= ', sd.droit '; 
			$join .= '
			LEFT JOIN structure_droit sd
				ON sd.structure = s.id
			'; 
			$where .= 'AND (sd.utilisateur='.$MEMBRE->id.'
			OR s.id = '.$MEMBRE->id_structure.' ) '; 
		}

		if( !is_null($this->fi_recherche) )
		{
			$where .= ' AND s.nom LIKE (\'%'.secubdd($this->fi_recherche).'%\') '; 
		}

		if( !empty($this->fi_ville) )
		{
			$where .=' AND ville='.$this->fi_ville.' '; 
		}

		$sql = '
			SELECT s.id, s.numero, s.actif, s.email mail, s.mail_rq, 
				s.banniere, s.banniere_url, presentation `desc`, s.facebook, s.type,
				s.logo, s.date_adhesion `date`, nom, 
				l.Lieu_Ville adresse__ville__nom, 
				l.Lieu_Dep adresse__ville__dep__num, 
				s.adresse adresse__rue
				'.$select.'
			FROM structure s
			LEFT OUTER JOIN Lieu l
				ON l.Lieu_ID = s.ville
			'.$join.'
			WHERE 1 
			'.$where.' 
			GROUP BY s.id 
			ORDER BY TRIM(nom)
		'; 
		
		parent::requete($sql); 
	}
}
