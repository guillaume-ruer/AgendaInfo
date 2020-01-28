<?php
class trace 
{
	public $table=''; 
	public $conf=NULL;

	function __construct($table, $conf)
	{
		$this->conf = $conf; 
		$this->table = $table; 
	}

	function insert($texte, $type)
	{
		global $MEMBRE; 
		static $p=NULL;

		$tab = debug_backtrace(); 

		if( is_null($p) )
		{
			$p = prereq('INSERT INTO '.$this->table.' (texte, date, type, idutr, fichier, ligne) VALUES(?,?,?,?,?, ?) ');
		}

		exereq($p, array($texte, time(), $type, $MEMBRE->id, basename($tab[0]['file']), $tab[0]['line'] ) ); 
	}

	function conf2def()
	{
		foreach($this->conf as $id => $type)
		{
			define( strtoupper($type['const']), $id ); 
		}
	}
}
