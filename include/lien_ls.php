<?php
class lien_ls extends reqo 
{
	private $type=0; 
	private $lieu=NULL; 
	private $limite=NULL; 

	function mut_type($type){ $this->type = (int)$type; } 
	function mut_lieu($lieu){ $this->lieu = noui($lieu); } 
	function mut_limite($limite){ $this->limite = noui($limite); } 

	function __construct($do=array())
	{
		parent::__construct($do); 
		$this->mut_sorti('lien'); 
		$this->mut_mode(reqo::NORMAL); 
	}

	function requete($null=NULL)
	{
		$where = $join = $order = ''; 

		if( !is_null($this->lieu) )
		{
			$where .='AND ( ll.id_lieu='.$this->lieu.' OR lg.id_lieu='.$this->lieu.' )'; 

			$join .= ' LEFT OUTER JOIN lien_lieu ll ON ll.id_lien = l.id ';  

			$join .=' 
				LEFT OUTER JOIN lien_lieu_grp llg ON llg.id_lien = l.id 
				LEFT OUTER JOIN Lieu_join lg ON lg.id_groupe = llg.id_lieu_grp
			'; 
		}

		if( !is_null($this->limite) )
		{
			$order .= ' ORDER BY l.titre LIMIT '.$this->limite.' '; 
		}
		else
		{
			$order .= ' ORDER BY l.titre '; 
		}

		$req = ' 
			SELECT l.id, l.url, img, l.titre 
			FROM lien l
			'.$join.'
			WHERE l.type='.$this->type.' 
			'.$where.'
			GROUP BY l.id 
			'.$order.' 
		'; 

		parent::requete($req); 
	}
}
