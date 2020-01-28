<?php

class adherent
{
	public $id=0;
	public $nom='';
	public $prenom=''; 
	public $structure=''; 
	public $adherent = ''; 
	public $logo = ''; 
	public $site = ''; 
	public $telephone = ''; 
	public $ville = ''; 
	public $ville_cp = ''; 
	public $id_ville = ''; 
	public $dep = 0; 
	public $adresse = ''; 
	public $mail = ''; 
	public $desc = ''; 
	public $type = ''; 
	public $date = 0; 

	function __construct()
	{
		if(func_num_args() >= 1 ) 
		{
			$this->init_bdd(func_get_arg(0) ); 
		}
	}

	function init_bdd($id)
	{
		$donne = req('
			SELECT c.id, c.adherent nom_adherent, c.logo, c.adresse, c.site, l.Lieu_Ville AS ville, c.Lieu id_ville, 
				Telephone telephone, l.Lieu_CP cp, c.Commentaires description, 
				c.Email mail, c.Prenom prenom, c.Nom nom, c.Entreprise structure, c.Departement dep,
				c.type, c.date_ajout date 
			FROM Contact AS c  
			LEFT JOIN Lieu AS l
				ON l.Lieu_ID = c.Lieu
					WHERE c.id = '.(int)$id.' 
			LIMIT 1 ');

		if( $do = fetch($donne) )
		{
			$this->id = absint($do['id']); 
			$this->adherent = secuhtml($do['nom_adherent']); 
			$this->logo = secuhtml($do['logo']);
			$this->adresse = secuhtml($do['adresse']); 

			//Traitement de l'adresse internet de l'adhérent 
			if(!empty($do['site']) )
			{
				$this->site = secuhtml(trim($do['site']) );  
				if( strpos($this->site, 'http://') !== 0 ) 
				{ 
					$this->site = 'http://'.$this->site;
				}
			}

			$this->telephone = secuhtml($do['telephone']);
			$this->ville = secuhtml($do['ville']);
			$this->ville_cp = (int)$do['cp']; 
			$this->id_ville = (int)$do['id_ville']; 
			$this->type = $do['type']; 
			$this->date = $do['date']; 
			$this->nom = $do['nom'];
			$this->prenom = $do['prenom']; 
			$this->structure = $do['structure']; 
			$this->dep = $do['dep'];
			$desc = $do['description'];

			$nbbr=6;
			if(substr_count($desc, "\n") >= $nbbr )
			{
				$numbr = 0; 	
				$pos = -1; 
				while( $numbr < $nbbr )
				{
					$pos = strpos($desc, "\n", $pos+1 ); 	
					$numbr++; 
				}

				$pos = ( $pos > 500 ) ? 500 : $pos - 1; 

				$this->desc = nl2br(secuhtml(substr($desc, 0, $pos ) ) ).' ...';
			}
			elseif(strlen($desc) > 500 )
			{
				$this->desc = nl2br(secuhtml(substr($desc, 0, 500 ) ) ).' ...';
			}
			else
			{
				$this->desc =nl2br(secuhtml($desc) ); 
			}

			$this->mail = secuhtml($do['mail']); 
		}
		else
		{
			mess("L'identifiant fournis ne correspond pas à un contact."); 
		}
	}

	function get_structure()
	{
		return $this->structure;
	}

	function get_dep()
	{
		return $this->dep; 
	}

	function get_date()
	{
		return date('Y-m-d', $this->date ); 
	}
}
