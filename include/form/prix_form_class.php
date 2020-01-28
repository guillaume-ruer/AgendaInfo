<?php

class prix_form extends champ_form 
{
	protected $size=0; 

	function mut_donne($donne)
	{
		$this->donne = is_null($donne) ? NULL : (float)$donne; 
	}

	function __construct($do = array() )
	{
		$this->size = 5; 
		parent::__construct($do); 
	}

	function size_chaine()
	{
		return empty($this->size) ? '' : ' size="'.$this->size.'" '; 
	}

	function aff_champ()
	{
		echo '<input id="'.$this->acc_identifiant().'" type="text" ';
		echo 'name="'.$this->acc_nom_champ().'" value="'.sprintf('%.2F', $this->donne).'" ';  
		echo $this->size_chaine().' />€'; 
	}

	function verif()
	{
		return TRUE; 
	}

	function recup()
	{
		if( is_null($this->indice) )
		{
			$this->donne = (float)$_POST[$this->nom()]; 
		}
		else
		{
			$this->donne = (float)$_POST[$this->nom()][$this->indice]; 
		}
	}
}
