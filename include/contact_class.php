<?php
require_once C_INC.'structure_class.php'; 

class contact extends identifiant 
{
	private $titre = ''; 
	private $tel = ''; 
	private $site = ''; 
	private $mail = ''; 
	private $structure=NULL; 

	function __construct($do=array() )
	{
		parent::__construct();
		$this->structure = new structure; 
		$this->hydrate($do); 
	}

	function acc_titre(){ return $this->titre; } 
	function acc_tel(){ return $this->tel; } 
	function acc_site(){ return $this->site; } 
	function acc_mail(){ return $this->mail; } 
	function acc_structure(){ return $this->structure; } 
	function acc_url(){ return url_adherent($this->structure->acc_id(), 0, ID_LANGUE, $this->structure->acc_nom() ); }	

	function mut_titre($t){ $this->titre = $t; } 
	function mut_tel($t){ $this->tel= $t; } 
	function mut_site($s){ $this->site=$s; }
	function mut_mail($m){ $this->mail= $m; }

	function mut_structure($s)
	{ 
		$this->rouh($this->structure, $s); 
	}

	function aff_site($lien=FALSE)
	{
		$site = trim($this->site); 

		if(!empty($site) )
		{   
			$site = secuhtml($site);  

			if( strpos($site, 'http://') !== 0 ) 
			{   
				$site = 'http://'.$site;
			}   

			echo $lien ? '<a href="'.$site.'" >'.$site.'</a>' : $site; 
		}   
	}

	function aff_titre(){ ps($this->titre); } 
	function aff_tel(){ ps($this->tel); } 

	function vide()
	{
		return !( $this->acc_titre() || $this->acc_tel() || $this->acc_site() ); 
	}
}


