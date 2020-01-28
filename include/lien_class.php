<?php

class lien extends identifiant
{
	private $type=0; 
	private $url='';
	private $img=''; 
	private $lieu=array(); 
	private $grp_lieu=array(); 
	private $titre=''; 

	private $maj_img=FALSE; 

	function maj_img()
	{
		return $this->maj_img; 
	}

	function mut_type($type){ $this->type = (int)$type; }
	function acc_type(){ return $this->type; }

	function mut_url($url){ $this->url = lien($url); }
	function acc_url(){ return $this->url; }
	function aff_url(){ ps( $this->url ); } 

	function mut_img($img)
	{ 
		$this->maj_img = TRUE; 
		$this->img = $img; 
	}

	function acc_img(){ return $this->img; }
	function aff_img()
	{ 
		if( !empty($this->img) )
		{
			echo '<img src="'.C_LIEN_IMG.secuhtml($this->img).'" alt="'.secuhtml($this->titre).'" title="'.secuhtml($this->titre).'" />'; 
		}
	} 

	function mut_lieu($lieu){ $this->lieu = array_map('intval', (array)$lieu); }
	function ajt_lieu($lieu){ $this->lieu[] = (int)$lieu; }
	function acc_lieu(){ return $this->lieu; }

	function mut_grp_lieu($grp_lieu){ $this->grp_lieu = array_map('intval', (array)$grp_lieu); }
	function ajt_grp_lieu($grp_lieu){ $this->grp_lieu[] = (int)$grp_lieu; }
	function acc_grp_lieu(){ return $this->grp_lieu; }

	function mut_titre($titre){ $this->titre = $titre; }
	function acc_titre(){ return $this->titre; }
	function aff_titre(){ ps( $this->titre ); } 

	function aff()
	{
		echo '<a href="'.secuhtml($this->url).'" >';
		if( empty($this->img) )
		{
			$this->aff_titre(); 
		}
		else
		{
			$this->aff_img(); 
		}
		echo '</a>'; 
	}
}
