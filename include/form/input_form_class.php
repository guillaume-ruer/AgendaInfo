<?php
abstract class input_form extends champ_form
{
	protected $size=0; 
	protected $type='text'; 

	function size_chaine()
	{
		return empty($this->size) ? '' : ' size="'.$this->size.'" '; 
	}

	function aff_champ()
	{
		echo '<input id="'.$this->acc_identifiant().'" type="'.$this->type.'" ';
		echo 'name="'.$this->acc_nom_champ().'" value="'.secuhtml($this->donne).'" ';  
		echo $this->size_chaine().' />'; 
	}
}
