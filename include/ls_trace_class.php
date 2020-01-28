<?php

class ls_trace
{
	/*
		Configuration 	
	*/

	private $type=NULL; 
	private $table='trace'; 
	private $conf=NULL; 
	private $page=0;
	private $nbtrace=20; 
	private $pagin_url=''; 

	function __construct($table, $conf)
	{
		$this->table = $table;
		$this->conf = $conf; 
	}

	function mut_page($page)
	{
		$this->page=$page; 
	}

	function mut_pagin_url($url)
	{
		$this->pagin_url=$url; 
	}

	function mut_table($t)
	{
		$this->table = $t; 
	}

	function mut_type($type)
	{
		if( $ret = ( is_null($type) || isset($this->conf[ $type ]) ) )
		{
			$this->type = $type; 
		}

		return $ret; 
	}

	function requete()
	{
		$where = ''; 

		if(!is_null($this->type ) )
		{
			$where = ' WHERE type='.$this->type.' '; 
		}

		$donne = new reqa('
			SELECT absint::t.id, trace_secu::t.texte, absint::t.type, madate::t.date, absint::t.idutr,
				secuhtml::t.fichier, absint::t.ligne,
				secuhtml::u.User pseudo
			FROM '.$this->table.' t
			LEFT JOIN Utilisateurs u
				ON t.idutr = u.id
			'.$where.'
			ORDER BY t.date DESC  
		',
		NULL, $this->page, $this->nbtrace, $this->pagin_url ); 
		
		return $donne; 
	}
}
