<?php 

class onglet_cocher_form extends form_liste 
{	
	private $tab_cb=array(); 

	function __construct($do=array())
	{
		parent::__construct($do); 
		ajt_script('onglet_cocher.js'); 
		ajt_style('onglet_cocher.css'); 
	}

	function ajt_onglet($cle, $val, $montre=FALSE ) 
	{
		parent::ajt($cle, $val); 
		$this->tab_cb[ $cle.'_cb' ] = new checkbox_form( array('donne'=> $montre ) ); 
	}

	function donne()
	{
		$res = array(); 

		foreach( $this->tab_champ as $cle => $val )
		{
			$res[$cle] = $this->tab_cb[$cle.'_cb']->donne() ? $val->donne() : FALSE; 
		}

		return $res; 
	}

	function aff()
	{
		$tab_cb = $this->tab_cb; 

		echo '<div class="oc_champ" >'; 

		foreach( $this->tab_champ as $cle => $champ ) 
		{
			echo '<div><div class="oc_case" ><label>';
			$this->aff_label_text(); 
			echo ' : ';
			$tab_cb[$cle.'_cb']->aff_champ(); 
			echo '<div class="oc_masquer" >'; 
				$champ->aff() 
			echo '</div></div>';
		}
		echo '</div>'; 
	}
}
