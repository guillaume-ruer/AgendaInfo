<?php
class visuel 
{
	// Général 
	private $conf=NULL;
	public $id;
	public $id_structure; 
	public $id_contact;
	private $type; 
	public $titre; 
	public $texte;
	public $url;
	public $img;
	public $datedeb;
	public $datefin; 

	// Expo 
	public $ville;

	// Pub 
	public $tab_ville=array(); 
	public $tab_grp_lieu=array(); 

	function __construct($conf)
	{
		$this->conf = $conf; 
	}

	function acc_type()
	{
		return $this->type; 
	}

	function mut_type($t)
	{
		if( isset($this->conf[ $t ]) )
		{
			$this->type = $t; 
			return TRUE; 
		}

		return FALSE;
	}

	function init($id)
	{
		$donne = req('SELECT * FROM Bandeaux WHERE id='.absint($id).' LIMIT 1 ');

		if($do = fetch($donne ) )
		{
			$this->id = absint($id);
			$this->id_structure = absint($do['id_structure']); 
			$this->titre = $do['Titre']; 
			$this->texte = $do['Texte'];
			$this->url = $do['URL'];
			$this->img = $do['Image'];
			$this->datedeb = $do['DateDeb'];
			$this->datefin = $do['DateFin']; 
			$this->id_contact = $do['contact']; 

			if( ($this->type = visuel_type2id($this->conf, $do['Type'])) === FALSE )
			{
				return FALSE; 
			}
			
			switch($this->conf[$this->type]['ville'] )
			{
				case VISUEL_VILLE_UNE :
					$this->ville = absint($do['ville']);
				break; 
				case VISUEL_VILLE_LISTE : 
					$ville = req('SELECT idville FROM pub_ville WHERE idpub='.absint($this->id).' '); 
					while($v = fetch($ville) )
					{
						$this->tab_ville[] = $v['idville']; 
					}

					$ville = req('SELECT id_grp_lieu FROM visuel_grp_lieu WHERE id_visuel='.absint($this->id).' '); 
					while($v = fetch($ville) )
					{
						$this->tab_grp_lieu[] = $v['id_grp_lieu']; 
					}
				break; 
			}

			return TRUE; 
		}
		else
		{
			return FALSE;
		}
	}

	function acc_id_structure()
	{
		return $this->id_structure; 
	}

	function mut_id_structure($id)
	{
		if(droit(GERER_UTILISATEUR) || str_droit($id) )
		{
			$this->id_structure = $id;
			return TRUE; 
		}
		else
		{
			return FALSE; 
		}
	}

	private function insert()
	{
		$ins = prereq('INSERT INTO Bandeaux(Type, Titre, Texte, Image, URL, DateDeb, DateFin, id_structure, ville, contact )
			VALUES(?,?,?,?,?, ?,?,?,?,? ) '); 
		exereq($ins, array($this->conf[$this->type]['type'], $this->titre, $this->texte, $this->img, $this->url, $this->datedeb, $this->datefin, 
			$this->id_structure, $this->ville, $this->id_contact ) ); 
		$this->id = derid(); 
		$this->insert_ville(); 
	}

	private function maj()
	{

		$maj = prereq('UPDATE Bandeaux SET Titre=?, Texte=?, Image=?, URL=?, DateDeb=?, DateFin=?, id_structure=?, ville=?, contact=? 
			WHERE id=? LIMIT 1 '); 
		exereq($maj, array($this->titre, $this->texte, $this->img, $this->url, $this->datedeb, $this->datefin, 
			$this->id_structure, $this->ville, $this->id_contact, $this->id ) ); 

		// Ajout des villes pour les pubs 
		$this->insert_ville(); 
	}

	function enr()
	{
		if( empty($this->id) )
		{
			/* On peut inséré si aucun droit n'est requis, où si on a le droit spécifique au type */
			$droit = $this->conf[ $this->type ]['droit']; 
			if( empty($droit) || droit( $droit ) )
			{
				$this->insert(); 
			}
		}
		else
		{
			/*
				On peut mêtre à jour si on a les droits sur les utilisateurs où que 
				le visuel apartient à une des structures que l'on gère 
			*/
			if( droit(GERER_UTILISATEUR) || str_droit($this->id_structure ) )
			{
				$this->maj(); 
			}
		}
	}

	function insert_ville()
	{
		static $iv = NULL, $sv, $ig, $sg;

		if(is_null($iv) )
		{
			$iv = prereq('INSERT INTO pub_ville(idpub, idville )VALUES(?,?) '); 
			$sv = prereq('DELETE FROM pub_ville WHERE idpub=?'); 

			$ig = prereq('INSERT INTO visuel_grp_lieu(id_visuel, id_grp_lieu) VALUES(?,?)'); 
			$sg = prereq('DELETE FROM visuel_grp_lieu WHERE id_visuel=?'); 
		}

		if( $this->conf[ $this->type ]['ville'] == VISUEL_VILLE_LISTE  )
		{
			exereq($sv, array( absint($this->id) ) );

			foreach($this->tab_ville as $v )
			{
				exereq($iv, array($this->id, $v) ); 
			}

			exereq($sg, array( absint($this->id) ) );

			foreach( $this->tab_grp_lieu as $g )
			{
				exereq($ig, array($this->id, $g) ); 
			}
		}
	}

	function sup($id)
	{
		static $sv=NULL; 

		if(is_null($sv) )
		{
			$sv=prereq('DELETE FROM Bandeaux WHERE id=? LIMIT 1 '); 
			$selids=prereq('SELECT id_structure FROM Bandeaux WHERE id=? LIMIT 1 '); 
		}

		if(droit(GERER_UTILISATEUR) )
		{
			exereq($sv, array($id) ); 
			$r=TRUE;
		}
		else
		{
			exereq($selids, array($id) ); 
			if( $do = fetch($selids) )
			{
				if(str_droit($do['id_structure']) )
				{
					exereq($sv, array($id) ); 
					$r=TRUE; 
				}
				else
				{
					$r=FALSE; 
				}
			}
			else
			{
				$r=FALSE; 
			}
		}

		return $r; 
	}
}
