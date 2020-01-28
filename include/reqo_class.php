<?php

class reqo extends objet_base 
{
	protected $donne=NULL; 
	private $tps; 
	private $requete=''; 
	protected $sorti='parcours_reqo'; 
	private $nb_par_page=20; 
	private $nb_entre=0; 
	private $avoir_nb_entre=FALSE; 
	private $mode=TRUE; 
	private $pagin=NULL; 
	private $num=0;
	private $switch=FALSE; 

	const NORMAL = 0;
	const PAGIN = 1; 
	const LIMITE = 2; 

	function __construct($do=array() )
	{
		parent::__construct(); 
		$this->pagin = new pagin_reqo(); 
		$this->hydrate($do); 
	}

	function acc_avoir_nb_entre(){ return $this->avoir_nb_entre; }
	function acc_num(){ return $this->num; } 
	function acc_pagin(){ return $this->pagin; }
	function acc_nb_par_page(){ return $this->nb_par_page; }
	function acc_num_page(){ return $this->num_page; }
	function acc_nb_page(){ return $this->pagin->acc_nb_page(); }
	function acc_sorti(){ return $this->sorti; } 
	function acc_nb_entre(){ return $this->nb_entre; }
	function acc_limite(){ return $this->limite; } 
	function acc_mode(){ return $this->mode; } 
	function acc_requete(){ return $this->requete; } 
	function acc_switch(){ return $this->switch; } 
	function acc_tps() { return $this->tps; } 

	function mut_avoir_nb_entre($a){ $this->avoir_nb_entre = (bool)$a; }
	function mut_nb_par_page($nb) { $this->nb_par_page = $nb>0 ? $nb : $this->nb_par_page; } 
	function mut_num_page($num){ $this->num_page = $num >= 0 ? $num : $this->num_page; } 
	function mut_sorti( $s ) { $this->sorti = $s; } 
	function mut_mode($m)
	{ 
		if( in_array($m, array(self::NORMAL, self::PAGIN, self::LIMITE ) ) )
		{
			$this->mode = $m; 
		}
	}
	function mut_pagin($pagin)
	{
		$this->crouh($this->pagin, $pagin, 'pagin_reqo' ); 
	}

	function requete($sql)
	{
		$tps = microtime(TRUE); 
		if( $this->mode == self::PAGIN || $this->avoir_nb_entre )
		{
			// On veut aussi le nombre de page pour afficher les liens de pagination 
			$nb_ligne = req('SELECT COUNT(*) nb FROM ('.$sql.' ) _nb_entre '); 
			$do = fetch($nb_ligne); 
			$this->nb_entre = (int)$do['nb']; 
		}

		if( $this->mode == self::PAGIN )
		{
			// Création de la limite 
			$min = $this->nb_par_page * $this->pagin->acc_num_page(); 
			$max = $min + $this->nb_par_page; 
			$sql .= ' LIMIT '.$min.','.$this->nb_par_page; 
			
			$this->pagin->mut_nb_page(ceil(abs( $this->nb_entre / $this->nb_par_page ) ) ); 
		}
		elseif( $this->mode == self::LIMITE )
		{
			$sql .=' LIMIT '.$this->nb_par_page; 
		}

		$this->donne = req($sql); 	
		$this->requete=$sql; 
		$this->tps = round( (microtime(TRUE)-$tps)*1000, 2); 
	}

	protected function suivant()
	{
		$this->switch = !$this->switch; 
		$this->num++; 
	}

	public function parcours()
	{
		$this->suivant(); 
		if( $do = fetch($this->donne) )
		{
			$ob = new $this->sorti( genere_init($do) ); 
			return $ob; 
		}
		else
		{
			return FALSE; 
		}
	}
}
