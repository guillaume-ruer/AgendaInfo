<?php
class identifiant extends objet_base
{
	private $id=0; 

	function mut_id($id){ $this->id = absint($id); }
	function acc_id(){ return $this->id; } 
	function aff_id(){ echo $this->id; }
}
