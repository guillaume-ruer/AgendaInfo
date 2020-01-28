<?php
require_once C_INC.'objet_class.php'; 

abstract class base_form extends objet 
{
	private static $num=0; 
	protected $label=''; 
	protected $id; 
	protected $donne=NULL; 
	protected $nom=''; 
	protected $import=TRUE; 
	protected $indice=NULL; 

	function __construct($do=array())
	{
		parent::__construct($do); 
		self::$num++; 
		$this->id=self::$num; 
	}

	function acc_identifiant()
	{ 
		$iden = $this->nom(); 
		if(!is_null($this->indice) )
		{
			$iden .= '_'.$this->indice; 	
		}

		return $iden; 
	}

	function acc_nom(){ return empty($this->nom) ? get_class($this).'_'.$this->id : $this->nom; } 
	function acc_donne(){ return $this->donne; } 

	abstract function mut_indice($indice); 
	abstract function recup(); 
	abstract function mut_donne($donne); 

	// Doit appeler donne() 
	abstract function verif(); 
	abstract function init_message(); 
	abstract function acc_message(); 
	abstract function message_fusion($message); 
	abstract function ajt_message_class($class); 

	function mut_import($bool)
	{
		$this->import = $bool;
	}

	final function donne()
	{
		if( $this->import )
		{
			$this->recup(); 
			$this->import=FALSE; 
		}

		return $this->donne; 
	}

	function acc_nom_champ()
	{
		$nom = $this->nom(); 

		if( !is_null($this->indice ) )
		{
			$nom .= '['.$this->indice.']'; 
		}

		return $nom; 
	}
}
