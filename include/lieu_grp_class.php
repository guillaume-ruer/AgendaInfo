<?php
class lieu_grp extends id_nom
{
	private $ordre=0; 
	private $tab_lieu=array(); 
	private $num = ''; 

	function mut_ordre($ordre){ $this->ordre = $ordre; }
	function acc_ordre(){ return $this->ordre; }

	function acc_tab_lieu(){ return $this->tab_lieu; } 
	function add_lieu($lieu){ $this->tab_lieu[] = (int)$lieu; }
	function mut_tab_lieu($tl){ $this->tab_lieu = $tl; } 	
	function def_tab_lieu(){ $this->tab_lieu = array(); } 

	function mut_num($num){ $this->num = $num; }
	function acc_num(){ return $this->num; }
	function aff_num(){ echo $this->acc_num(); }
}
