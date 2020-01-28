<?php

class coche_form extends champ_form
{
	function recup()
	{
		if( is_null($this->indice) )
		{
			$this->donne = isset($_POST[$this->nom()] ); 
		}
		else
		{
			$this->donne = isset($_POST[$this->nom()][$this->indice]); 
		}
	}

	function mut_donne($d)
	{
		$this->donne = (bool)$d; 
	}

	function aff_champ()
	{
		$checked = $this->donne ? 'checked="checked"' : ''; 
		echo '<input id="'.$this->acc_identifiant().'" name="'.$this->acc_nom_champ().'" type="checkbox" '.$checked.' />';
	}

	function aff()
	{
		echo '<p>'; 
		$this->aff_label();
		$this->aff_champ(); 
		$this->message->aff_ligne(); 
		echo "\n</p>\n"; 
	}
}

