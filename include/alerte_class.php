<?php
class alerte extends identifiant 
{
	private $idevent;
	private $cause;
	private $etat;
	private $time;
	private $type; 
	private $titre; 

	function acc_idevent(){ return $this->idevent; }
	function acc_cause(){ return $this->cause;}
	function acc_etat(){ return $this->etat; } 
	function acc_time(){ return $this->time; }
	function acc_type(){ return $this->type; } 
	function acc_titre(){ return $this->titre;}


	function mut_idevent($id){  $this->idevent=(int)$id; }
	function mut_cause($c){  $this->cause=$c;}
	function mut_etat($e){  $this->etat=(int)$e; } 
	function mut_time($t){  $this->time=(int)$t; }
	function mut_type($t){  $this->type=(int)$t; } 
	function mut_titre($t){ $this->titre = $t; }
}
