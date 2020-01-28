<?php
require_once C_INC.'lien_grp_class.php'; 

class lien_grp_ls extends reqo
{
	private $rejet=NULL;
	private $non_vide=FALSE; 
	private $fi_lieu = NULL; 
	private $lien=FALSE; 

	function __construct($do=array() )
	{
		parent::__construct($do); 
		$this->mut_sorti('lien_grp'); 
		$this->mut_mode(reqo::NORMAL); 
	}

	function mut_rejet($rejet){ $this->rejet = noui($rejet); }
	function mut_non_vide($non_vide){ $this->non_vide= $non_vide;} 
	function mut_fi_lieu( $fi_lieu ){ $this->fi_lieu = noui($fi_lieu); } 
	function mut_lien($lien){ $this->lien = (bool)$lien; }

	function requete($null=NULL)
	{
		req('SET SESSION group_concat_max_len = 16384');
		$where = $join = $group = $having = $select = ''; 

		if( !is_null($this->rejet) )
		{
			$where .= ' AND lg.id != '.$this->rejet.' '; 
		}

		if( $this->non_vide || !is_null($this->fi_lieu) || $this->lien )
		{
			$join .= ' JOIN lien l ON l.type=lg.id '; 
			$group .= ' GROUP BY lg.id ';
		}

		if( $this->non_vide  )
		{
			$having = ' HAVING COUNT(*) > 0 '; 
		}

		if( !is_null($this->fi_lieu ) )
		{
			$where .='AND ( ll.id_lieu='.$this->fi_lieu.' OR lj.id_lieu='.$this->fi_lieu.' )'; 

			$join .= ' LEFT OUTER JOIN lien_lieu ll ON ll.id_lien = l.id ';  

			$join .=' 
				LEFT OUTER JOIN lien_lieu_grp llg ON llg.id_lien = l.id 
				LEFT OUTER JOIN Lieu_join lj ON lj.id_groupe = llg.id_lieu_grp
			'; 

		}

		if( $this->lien )
		{
			$select .= ', GROUP_CONCAT( 
				\'titre=\', l.titre, \'_&_url=\', l.url, \'_&_img=\', l.img 
				ORDER BY l.titre
				SEPARATOR \';;\' 
			) lien_ls '; 
		}

		$req = '
			SELECT lg.id, lg.nom '.$select.'
			FROM lien_grp lg
			'.$join.'
			WHERE 1 '.$where.'
			'.$group.'
			'.$having.'
			ORDER BY lg.nom
		'; 

		parent::requete( $req ); 
	}

	function parcours()
	{
		$this->suivant(); 
		if( $do = fetch($this->donne) )
		{
			$ob = new $this->sorti( genere_init($do) ); 
			if( $this->lien )
			{
				$ob->str2ls($do['lien_ls']); 
			}
			return $ob; 
		}
		else
		{
			return FALSE; 
		}
	}
}
