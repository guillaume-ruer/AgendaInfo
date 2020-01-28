<?php

class deroulant_form extends champ_form
{
	protected $tab_option=array(); 

	function mut_donne($donne){ $this->donne = $donne; } 

	function aff_champ()
	{
		echo '<select id="'.$this->acc_identifiant().'" name="'.$this->acc_nom_champ().'" >'; 
		foreach($this->tab_option as $cle => $val )
		{
			echo '<option value="'.secuhtml($cle).'" '.($cle == $this->donne ? 'selected="selected"' : '' ).' >'.secuhtml($val).'</option>'; 
		}
		echo '</select>'; 
	}

	function aff()
	{
		$this->message->aff(); 
		echo '<p class="form_deroulant" >'; 
		$this->aff_label();
		$this->aff_champ(); 
		$this->message->aff_ligne(); 
		echo "\n</p>\n"; 

	}

	function verif()
	{
		$donne = $this->donne(); 
		return isset($this->tab_option[$donne]); 
	}

	function mut_tab_option($do)
	{
		if( is_array($do) )
		{
			$this->tab_option = $do; 
		}
		else
		{
			trigger_error('Tableau attendu.'); 
		}
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

}
