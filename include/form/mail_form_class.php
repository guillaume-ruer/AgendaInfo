<?php

class mail_form extends chaine_form
{
	function __construct( $do=array() )
	{
		$this->type='email'; 
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

		if( !empty($donne) && filter_var($donne, FILTER_VALIDATE_EMAIL)===FALSE )
		{
			$this->mess( c('Le champ %label doit contenir une adresse mail.') ); 
			$v=FALSE; 	
		}

		return $v; 
	}
}
