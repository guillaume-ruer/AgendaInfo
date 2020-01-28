<?php

class tel_form extends chaine_form
{
	function __construct($do = array() )
	{
		$this->size = 10; 
		parent::__construct($do); 
	}

	function verif()
	{
		$v = TRUE; 
		$donne = $this->donne(); 

		if( !parent::verif() )
		{
			$v = FALSE; 
		}

		if( !empty($donne) && !preg_match('`^\+?([0-9]+[ _.-])*[0-9]+$`', $donne) )
		{
			$this->mess('Le champ %label doit contenir un numéro de téléphone.');
			$v=FALSE; 	
		}

		return $v; 
	}
}
