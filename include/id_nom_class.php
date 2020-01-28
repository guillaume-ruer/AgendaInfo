<?php
class id_nom extends identifiant 
{
	private $nom; 

	function acc_nom(){ return $this->nom; } 
	function mut_nom($n){ $this->nom = $n; } 
	function aff_nom(){ ps( $this->nom ); }
}
