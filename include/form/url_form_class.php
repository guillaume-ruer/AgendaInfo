<?php

class url_form extends chaine_form
{
	function __construct( $do=array() )
	{
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

		if( !empty($donne) && filter_var($donne, FILTER_VALIDATE_URL)===FALSE )
		{
			$this->mess('Le champ %label doit contenir une url valide.'); 
			$v=FALSE; 	
		}

		return $v; 
	}
}
