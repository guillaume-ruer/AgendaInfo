<?php

class radio_form extends champ_form
{
	protected $tab_option=array(); 

	function mut_donne($donne){ $this->donne = $donne; } 

	function aff()
	{
		echo '<fieldset>
			<legend>'.$this->label.'</legend>'."\n"; 

		foreach($this->tab_option as $cle => $val )
		{
			echo '<input id="'.$this->acc_identifiant().'_'.$cle.'" type="radio" name="'.$this->acc_nom_champ().'" value="'.$cle.'" ';
			if( $cle == $this->donne )
			{
				echo ' checked="checked" '; 
			}
			echo '/>'; 
			echo ' : <label for="'.$this->acc_identifiant().'_'.$cle.'" >'.$val .'</label><br />'; 
		}

		echo '</fieldset>'; 
	}

	function aff_champ()
	{
		foreach($this->tab_option as $cle => $val )
		{
			echo '<input id="'.$this->acc_identifiant().'_'.$cle.'" type="radio" name="'.$this->acc_nom_champ().'" value="'.$cle.'" ';
			if( $cle == $this->donne )
			{
				echo ' checked="checked" '; 
			}
			echo '/>'; 
			echo ': <label for="'.$this->acc_identifiant().'_'.$cle.'" >'.$val .'</label>'; 
		}
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
