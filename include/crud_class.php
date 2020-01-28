<?php

function crud_ch($nom_ob, $nom_ch=NULL, $option=0)
{
	return array($nom_ob, is_null($nom_ch)?$nom_ob:$nom_ch, $option ); 
}

function crud_op($nom_ob, $option=0)
{
	return crud_ch( $nom_ob, NULL, $option);
}


class crud extends objet_base
{
	// champ bdd => méthode acces
	private $tab_champ=array(); 
	private $table=''; 

	private $decoration=NULL; 

	const RECURSIVE=0x1;
	const HERITAGE=0x2; 
	const MAJ=0x4; 
	const INS=0x8; 
	const TOUT = 0xFFF; 

	function __construct($tab_champ, $table, $do=array() )
	{
		parent::__construct($do); 
		$this->tab_champ = $tab_champ; 
		$this->table = $table; 
	}

	function mut_tab_champ($tab_champ) { $this->tab_champ = $tab_champ; }
	function mut_table( $table ){ $this->table = $table; } 
	function mut_decoration( $decoration ){ $this->decoration=$decoration; } 

	function ins($ob)
	{
		if( !is_null($this->decoration) )
		{
			$this->decoration->ins($ob); 
		}

		if( empty($this->tab_champ) )
		{
			return TRUE; 
		}

		$tab_ch = $tab_val = array(); 
		foreach($this->tab_champ as $conf )
		{
			list( $nom_ob, $nom_ch, $opt ) = is_array($conf) ? $conf : crud_op($conf, self::TOUT ); 

			if( !($opt & self::INS) )
				continue; 
			
			$tab_ch[]='`'.(is_null($nom_ch) ? $nom_ob : $nom_ch).'`';

			if( is_object( $v = $ob->{'acc_'.$nom_ob}() ) )
			{
				if( $opt & self::RECURSIVE )
				{
					$func = get_class($v).'_crud'; 
					$func()->enr($v); 
				}

				$tab_val[] = $v->acc_id(); 
			}
			else
			{
				$tab_val[] = $v; 
			}
		}

		$req = 'INSERT INTO '.$this->table.'('.implode(',', $tab_ch).') 
			VALUES(?'.str_repeat(',?', count($tab_ch)-1).')'; 

		exepre($req, $tab_val); 

		if( $ob->acc_id() == 0 )
			$ob->mut_id( derid() ); 

	}

	function maj($ob)
	{
		if( !is_null($this->decoration) )
		{
			$this->decoration->maj($ob); 
		}

		if( empty($this->tab_champ) )
		{
			return TRUE; 
		}

		$tab_ch = $tab_val = array(); 
		foreach($this->tab_champ as $conf )
		{
			list( $nom_ob, $nom_ch, $opt ) = is_array($conf) ? $conf : crud_op($conf, self::TOUT ); 

			if(  $nom_ob=='id' )
			{
				$identifiant = $nom_ch; 
				continue; 
			}

			if( !($opt & self::MAJ) )
				continue; 

			$nom = is_null($nom_ch) ? $nom_ob : $nom_ch; 
			$tab_ch[] = '`'.$nom.'`=:'.$nom; 

			if( is_object( $v = $ob->{'acc_'.$nom_ob}() ) )
			{
				if( $opt & self::RECURSIVE )
				{
					$func = get_class($v).'_crud'; 
					$func()->enr($v); 
				}

				$tab_val[$nom] = $v->acc_id(); 
			}
			else
			{
				$tab_val[$nom] = $v; 
			}
		}
		
		$tab_val['id'] = $ob->acc_id();

		if( !isset($identifiant) )
			$identifiant='id'; 

		$req = 'UPDATE '.$this->table.' 
			SET '.implode(',', $tab_ch).' WHERE '.$identifiant.'=:id'; 
		exepre($req, $tab_val); 
	}
	
	function enr($ob)
	{
		$ob->acc_id()==0 ? $this->ins($ob) : $this->maj($ob); 
	}
}
