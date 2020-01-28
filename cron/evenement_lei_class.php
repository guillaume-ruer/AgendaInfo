<?php

class evenement_lei extends evenement 
{
	private $der_verif = 0; 
	private $h_com=NULL;
	private $h_titre=NULL;
	private $h_theme=NULL;
	private $h_contact=NULL;
	private $h_lieu = NULL; 
	private $date_lei = array(); 

	function acc_der_verif(){ return $this->der_verif; }
	function acc_h_com(){ return $this->h_com; }
	function acc_h_titre(){ return $this->h_titre; }
	function acc_h_theme(){ return $this->h_theme; }
	function acc_h_contact(){ return $this->h_contact; }
	function acc_h_lieu(){ return $this->h_lieu; } 
	function acc_date_lei(){ return $this->date_lei; }

	function mut_der_verif($dv){ $this->der_verif = (int)$dv; }
	function mut_h_com($com){ $this->h_com = (string)$com; }
	function mut_h_titre($t){ $this->h_titre = (string)$t; }
	function mut_h_theme($t){ $this->h_theme = (string)$t; }
	function mut_h_contact($c){ $this->h_contact = (string)$c; }
	function mut_h_lieu($l) { $this->h_lieu = (string)$l; } 
	function mut_date_lei($dl){ $this->date_lei = $dl; } 
}
