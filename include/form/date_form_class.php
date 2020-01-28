<?php

class date_form extends champ_form 
{
	protected $max = NULL; 
	protected $min = NULL; 

	function mut_donne($date)
	{
		$this->donne = is_null($date) ? NULL : (int)$date; 
	}

	function mut_min($min)
	{
		$this->min = is_null($min) ? NULL : (int)$min; 
	}

	function mut_max($max)
	{
		$this->max = is_null($max) ? NULL : (int)$max; 
	}

	function aff_champ()
	{
		list($j, $m, $a) = (empty($this->donne) ? array(0,0,0) : explode('/', date('d/m/Y', $this->donne) ) ); 

		echo '<select name="'.$this->acc_nom_champ().'[0]" >'; 
		echo '<option value="" >Jour</option>'; 
		for($i=1; $i<=31; $i++)
		{
			echo '<option value="'.$i.'" '.($i==$j ? 'selected="selected"' : '' ).' >'.$i.'</option>'; 
		}
		echo '</select>/'; 

		echo '<select name="'.$this->acc_nom_champ().'[1]" >'; 
		echo '<option value="" >Mois</option>'; 
		for($i=1; $i<=12; $i++)
		{
			echo '<option value="'.$i.'" '.($i==$m ? 'selected="selected"' : '' ).'>'.moi_num2str($i).'</option>'; 
		}
		echo '</select>/'; 

		echo '<select name="'.$this->acc_nom_champ().'[2]" >'; 
		echo '<option value="" >Année</option>'; 
		for($i=2000; $i<=(int)date('Y'); $i++)
		{
			echo '<option value="'.$i.'" '.($i==$a ? 'selected="selected"' : '' ).'>'.$i.'</option>'; 
		}
		echo '</select>'; 
	}

	function recup()
	{
		$this->donne=NULL; 

		if( is_null($this->indice) )
		{
			$donne = (array)$_POST[$this->nom()]; 
		}
		else
		{
			$donne = (array)$_POST[$this->nom()][$this->indice]; 
		}

		
		list($j, $m, $a) = $donne; 

		if( !(empty($j) || empty($m) || empty($a) ) )
		{
			$this->donne = mktime(0,0,0,$m, $j, $a); 
		}
	}

	function verif()
	{
		$this->donne(); 
		$v=TRUE; 
		/*

		if( !is_null($this->max) && ($nbc > $this->max) )
		{
			$v = FALSE; 
			$this->mess(str_replace('%d', $this->max, 'Le champs %label doit comporter moins de %d caractères.') ); 
		}

		if( !is_null($this->min) && ($nbc < $this->min) )
		{
			$v = FALSE; 
			$this->mess(str_replace('%d', $this->min, 'Le champs %label doit comporter plus de %d caractères.') ); 
		}
		*/

		if( !parent::verif() )
		{
			$v = FALSE; 
		}

		return $v; 
	}
}
