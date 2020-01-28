<?php
class categorie extends id_nom
{
	private $img='';
	private $groupe=0; 
	private $groupe_nom=''; 
	private $width = 0; 
	private $height = 0; 

	function acc_groupe(){ return $this->groupe; }
	function mut_groupe($groupe){ $this->groupe = $groupe; }

	function acc_groupe_nom(){ return $this->groupe_nom; }
	function mut_groupe_nom($groupe_nom){ $this->groupe_nom = $groupe_nom; }

	function acc_img(){ return $this->img; } 
	function mut_img($img){ $this->img = $img; }

	function acc_width(){ return $this->width; } 
	function mut_width($width){ $this->width = (int)$width; }

	function acc_height(){ return $this->height; } 
	function mut_height($height){ $this->height = (int)$height; }

	function aff()
	{
		echo $this->chaine(); 
	}

	function chaine()
	{
		$attr = ''; 
		if( !empty($this->width) ) 
		{
			$attr .= ' width="'.(int)$this->width.'" '; 
		}

		if( !empty($this->height ) ) 
		{
			$attr .= ' height="'.(int)$this->height.'" '; 
		}

		return '<img class="ev-cat-img" src="'. ADD_SITE_DYN. 'img/symboles/'. secuhtml($this->img). '" alt="'. 
			secuhtml($this->acc_nom() ). '" title="'. secuhtml($this->acc_nom() ). '" '.$attr.' />'; 
	}
}
