<?php
require_once C_INC.'ville_class.php'; 

class lieu_ls extends reqo
{
	private $fi_debut=''; 
	private $fi_dep=NULL; 
	private $fi_groupe=NULL; 

	function __construct($do=array())
	{
		$this->mut_sorti('ville'); 
		$this->mut_mode(parent::NORMAL); 
		parent::__construct($do); 
	}

	function mut_fi_debut($fi_debut){ $this->fi_debut = $fi_debut; }
	function mut_fi_dep($fi_dep){ $this->fi_dep = is_null($fi_dep)?NULL:(int)$fi_dep; }
	function mut_fi_groupe($fi_groupe){ $this->fi_groupe = is_null($fi_groupe)?NULL:(int)$fi_groupe; }

	function requete($var=NULL)
	{
		$where = ''; 
		$join = ''; 

		if( !empty($this->fi_debut) )
		{
			$where .= 'AND Lieu_Ville LIKE( \''.$this->fi_debut.'%\') '; 
		}

		if( !is_null($this->fi_dep) )
		{
			$where .= 'AND Lieu_Dep='.$this->fi_dep.' '; 
		}

		if( !is_null($this->fi_groupe) )
		{
			$join .= ' JOIN Lieu_join ON id_lieu = Lieu_ID '; 
			$where .= 'AND Lieu_join.id_groupe='.$this->fi_groupe.' '; 
		}

		parent::requete('
			SELECT Lieu_ID id, Lieu_Ville nom, Lieu_Dep dep__num 
			FROM Lieu 
			'.$join.'
			WHERE 1  
			'.$where.'
			ORDER BY nom
		'); 
	}
}
