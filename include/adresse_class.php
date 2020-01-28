<?php
require_once C_INC.'ville_class.php'; 

class adresse extends objet_base
{
	private $rue; 
	private $ville; 

	function __construct($do=array() )
	{
		parent::__construct();
		$this->ville = new ville; 
		$this->hydrate($do); 
	}

	function acc_rue(){ return $this->rue; }
	function acc_ville(){ return $this->ville; } 

	function mut_rue($rue){ $this->rue = $rue; } 
	function mut_ville($v)
	{
		$this->rouh($this->ville, $v); 
	}
}
