<?php
require_once C_INC.'departement_class.php'; 
require_once C_INC.'proposition_interface.php'; 

class ville extends objet implements proposition 
{
	protected $id = 0;
	protected $nom = ''; 
	protected $url='';
	protected $dep='__departement';
	protected $cp=0; 
	protected $site=''; 
	protected $facebook=''; 
	protected $wikipedia=''; 
	protected $image1=''; 
	protected $google_map=''; 
	protected $mairie=0;
	protected $office=0; 
	protected $tab_grp=array(); 
	protected $desc=''; 
	protected $lat = 0.0; 
	protected $long = 0.0; 

	function acc_url(){ return $this->url; } 
 
	function acc_cp(){ return $this->cp; } 
	function acc_site(){ return $this->site; }
	function acc_facebook(){ return $this->facebook; }
	function acc_wikipedia(){ return $this->wikipedia; }
	function acc_image(){ return $this->image1; } 
	function acc_google_map(){ return $this->google_map; } 
	function acc_mairie(){ return $this->mairie; }
	function acc_office(){ return $this->office; } 
	function acc_tab_grp(){ return $this->tab_grp; } 
	function acc_desc(){ return $this->desc; } 
	function acc_lat(){ return $this->lat; } 
	function acc_long(){ return $this->long; } 

	function mut_url($url){ $this->url = $url;} 

	function mut_cp( $cp ){ $this->cp = absint($cp); } 
	function mut_site( $s ){ $this->site = $s; } 
	function mut_facebook( $f ){ $this->facebook = $f; } 
	function mut_wikipedia($w){ $this->wikipedia = $w;} 
	function mut_image($i){ $this->image1 = $i; } 
	function mut_google_map($g){ $this->google_map = $g; } 
	function mut_mairie($m){ $this->mairie = $m; } 
	function mut_office($o){ $this->office = $o; }

	function add_grp($grp){ $this->tab_grp[] = (int)$grp; } 
	function mut_tab_grp($tg){ $this->tab_grp = (array)$tg; } 
	function def_tab_grp(){ $this->tab_grp = array(); }

	function mut_desc($desc){ $this->desc = $desc; } 
	function mut_lat($lat){ $this->lat = (float)$lat; } 
	function mut_long($long){ $this->long = (float)$long; } 

	function tab()
	{
		return [
			'nom' => $this->acc_nom(),
			'id' => $this->acc_id(),
			'lat' => $this->lat, 
			'long' => $this->long
		];
	}

	function etiquette()
	{
		return $this->proposition(); 
	}

	function json()
	{
		return json_encode( array('nom' => $this->nom, 'id' => $this->id, 'dep__num' => $this->dep()->acc_num() ) ); 
	}

	function proposition()
	{
		return $this->acc_nom().' ('.$this->dep()->acc_num().')'; 
	}
}
