<?php

class chaine_form extends champ_form 
{
	protected $size=0; 
	protected $type='text'; 
	protected $max = NULL; 
	protected $min = NULL; 

	function mut_min($min)
	{
		$this->min = is_null($min) ? NULL : (int)$min; 
	}

	function mut_max($max)
	{
		$this->max = is_null($max) ? NULL : (int)$max; 
	}

	function size_chaine()
	{
		return empty($this->size) ? '' : ' size="'.$this->size.'" '; 
	}

	function aff_champ()
	{
		echo '<input id="'.$this->acc_identifiant().'" type="'.$this->type.'" placeholder="'.$this->label.'" ';
		$value = $this->type == 'password' ? '' : secuhtml($this->donne) ; 
		echo 'name="'.$this->acc_nom_champ().'" value="'.$value.'" ';  
		echo $this->requis ? ' required="required" ' : ''; 
		echo $this->size_chaine().' />'; 
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

	function mut_donne($do)
	{
		$this->donne = is_null($do)? NULL:  (string)$do; 
	}

	function verif()
	{
		$this->donne(); 
		$nbc = strlen($this->donne); 
		$v=TRUE; 

		if( !is_null($this->max) && ($nbc > $this->max) )
		{
			$v = FALSE; 
			$this->mess(str_replace('%d', $this->max, 'Le champs %label doit comporter moins de %d caractères.') ); 
		}

		if( !is_null($this->min) && ($nbc < $this->min) )
		{
			$v = FALSE; 
			$this->mess(str_replace('%d', $this->min, 'Le champs %label doit comporter au moins de %d caractères.') ); 
		}

		if( !parent::verif() )
		{
			$v = FALSE; 
		}

		return $v; 
	}
}
