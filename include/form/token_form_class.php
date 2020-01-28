<?php

class token_form extends champ_form 
{
	protected $token=NULL; 

	function __construct($do=array())
	{
		parent::__construct($do); 
	}

	function aff()
	{
		$this->aff_champ(); 
	}

	function aff_champ()
	{
		$this->token = bin2hex(openssl_random_pseudo_bytes(15) ); 
		$_SESSION['antispam_token'] = $this->token; 
		$_SESSION['antispam_time'] = time(); 
		echo '<input type="hidden" name="'.$this->acc_nom_champ().'" value="'.$this->token.'" />'; 
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
		$v=TRUE; 


		if(!isset($_SESSION['antispam_token']) || ($_SESSION['antispam_token'] != $this->donne) )
		{
			$v = FALSE; 
			debug('Token ne passe pas'); 
		}
		else
		{
			debug('Token passe'); 
		}

		if(!isset($_SESSION['antispam_time']) || ((time() - $_SESSION['antispam_time'] ) < 15) )
		{
			$v = FALSE; 
			debug('Moins de 15 secondes.'); 
		}
		else
		{
			debug('Plus de 15 secondes.'); 
		}

		return $v; 
	}
}
