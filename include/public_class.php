<?php
class event_public extends identifiant
{
	static $TAB_NOM=array( 
		'Non défini', 'Jeune public', 'Adolescent', 
		'Adulte', 'Adolescent et adulte', 'Jeune public et adolescent', 
		'Tout public',
	); 

	static $TAB_IMAGE=array( 
		'p-non-defini.jpg', 'p-jeune-public.jpg', 'p-adolescent.jpg', 
		'p-adulte.jpg', 'p-adolescent-adulte.jpg', 'p-jeune-public-adolescent.jpg', 
		'p-tout-public.jpg',
	);

	function acc_nom(){ return self::$TAB_NOM[$this->acc_id() ]; }
	function acc_img(){ return self::$TAB_IMAGE[ $this->acc_id() ]; }

	function aff_nom(){ echo $this->acc_nom(); }
	function aff_img(){ echo $this->acc_img(); }

	function mut_id($id)
	{
		if( isset(self::$TAB_NOM[$id]) )
		{
			parent::mut_id($id); 
		}
	}

}
