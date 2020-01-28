<?php
class tarif extends identifiant 
{
	static $TARIF_IMG = array('e-gratuit.jpg', 'e-participation-libre.jpg', 'e-pas-indique.jpg', 'e-payant-gratuit.jpg', 'e-payant.jpg'); 
	static $TARIF_NOM = array('Gratuit', 'Participation libre', 'Tarif non indiqué', 'Payant et gratuit', 'Payant'); 

	function __construct($do=array() )
	{
		parent::__construct(); 
		$this->mut_id(2); 
		$this->hydrate($do); 
	}

	function mut_id($id)
	{
		if( isset(self::$TARIF_IMG[$id], self::$TARIF_NOM[$id]) )
		{
			parent::mut_id($id); 
			return TRUE; 
		}
		return FALSE; 
	}

	function acc_nom()
	{ 
		return self::$TARIF_NOM[$this->acc_id()]; 
	}
	
	function acc_img()
	{ 
		return self::$TARIF_IMG[$this->acc_id()]; 
	}

	function aff()
	{
		echo '<img  src="',C_IMG,'tarifs/', $this->acc_img(), '" alt="', $this->acc_nom(), 
			'" title="', $this->acc_nom(), '" />'; 
	}
}
