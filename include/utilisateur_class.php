<?php
class utilisateur extends identifiant
{
	private $nom=''; 

	function acc_nom(){ return $this->nom; }

	function mut_nom($n){ $this->nom = $n; } 
}
