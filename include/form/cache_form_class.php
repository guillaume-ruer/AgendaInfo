<?php

class cache_form extends champ_form
{
	function recup()
	{
		if( is_null($this->indice) )
		{
			$this->donne = (string)$_POST[$this->nom()]; 
		}
		else
		{
			$this->donne = (string)$_POST[$this->nom()][$this->indice]; 
		}
	}

	function mut_donne($donne)
	{
		$this->donne = $donne; 
	}

	function aff()
	{
		echo '<input id="'.$this->acc_nom_champ().'" type="hidden" name="'.$this->acc_nom_champ().'" value="'.secuhtml($this->donne).'" />'; 
	}

	function aff_champ()
	{
		$this->aff(); 
	}

	function verif()
	{
		$donne = $this->donne(); 
		return TRUE; 
	}
}
