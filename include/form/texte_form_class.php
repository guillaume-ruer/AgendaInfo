<?php

class texte_form extends champ_form 
{
	protected $rows=7; 
	protected $cols=40; 
	protected $max=NULL; 

	function mut_donne($donne)
	{
		$this->donne = is_null($donne) ? NULL : (string)$donne; 
	}

	function verif()
	{
		return parent::verif(); 
	}

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

	function aff_champ()
	{
		$autofocus = $this->autofocus ? 'autofocus="autofocus"' : ''; 
		echo '<textarea id="'.$this->acc_identifiant().'" name="'.$this->acc_nom_champ().'"',
			' rows="'.$this->rows.'" cols="'.$this->cols.'" '.$autofocus.' >'.secuhtml($this->donne).'</textarea>'; 
	}
}
