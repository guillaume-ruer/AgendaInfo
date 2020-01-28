<?php
require_once C_INC.'form/base_form_class.php'; 
require_once C_INC.'message_class.php'; 

abstract class champ_form extends base_form 
{
	protected $message = NULL; 
	protected $requis = FALSE; 
	protected $message_class = 'message_erreur'; 
	const CHAINE_REQUIS = '<span class="form_requis" >(*)</span>'; 
	protected $exp = ''; 
	protected $autofocus=FALSE; 

	function __construct($do = array() )
	{
		$this->message = new message; 
		parent::__construct($do); 
	}

	function init_message()
	{
		$this->message = new message(); 
	}

	function mut_indice($indice)
	{
		$this->indice = $indice; 
	}

	function ajt_message_class($class)
	{
		$this->message_class .= ' '.$class; 
	}

	function acc_message_class()
	{
		return $this->message_class; 
	}

	function acc_message()
	{
		return $this->message; 
	}

	function verif()
	{
		$v = TRUE; 
		$donne = $this->donne(); 

		if( $this->requis && empty($donne) )
		{
			$this->mess('Le champ %label est requis.'); 
			$v = FALSE; 
		}

		return $v; 
	}

	abstract function aff_champ(); 

	function message_fusion($message)
	{
		$this->message->fusion($message); 
	}

	function mess($mess, $class=NULL)
	{
		$c = $this->message_class; 
		if( !is_null($class) )
		{
			$c .= ' '.$class; 
		}

		$mess = str_replace('%label', '<span class="form_nom_champ" >'.$this->label.'</span>', $mess); 
		$this->message->ajt($mess, $c); 
	}

	function aff_message()
	{
		$this->message->aff(); 
	}

	function chaine_requis()
	{
		return $this->requis ? self::CHAINE_REQUIS : ''; 
	}

	function aff_label()
	{
		echo '<label for="'.$this->acc_identifiant().'" >'.$this->label.' '.$this->chaine_requis(); 
		if( !empty($this->exp) )
		{
			echo '<span data-tip="'.$this->exp.'" >?</span>'; 
		}
		echo ' : </label>'; 
	}

	function aff()
	{
		//echo '<p>'; 
		$this->aff_label();
		//echo "\n</p>\n"; 
		echo "\n<br />\n"; 

		$this->message->aff(); 

		//echo "<p>"; 
		$this->aff_champ(); 
		//echo "\n</p>\n"; 
		echo "\n<br />\n"; 
	}
}
