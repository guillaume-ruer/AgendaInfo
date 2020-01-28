<?php
class objet_base
{

	function __construct($do = array() )
	{
		$this->hydrate($do); 
	}

	function hydrate($do = array() )
	{
		foreach( $do as $cle => $val )
		{
			$met = 'mut_'.$cle; 

			if( method_exists($this, $met ) )
			{
				$this->$met($val); 
			}
		}
	}

	// Remplace ou hydrate 
	function rouh(&$attr, $val)
	{
		if( est_class($val, get_class($attr) ) )
		{
			$attr = $val; 
		}
		elseif( is_array($val) )
		{
			$attr->hydrate($val); 
		}
	}

	// Crée, remplace ou hydrate 
	function crouh(&$attr, $val, $class )
	{
		if( est_class($val, $class ) )
		{
			$attr = $val; 
		}
		elseif( is_array($val) ) 
		{
			if( is_null($attr) )
			{
				$attr = new $class($val); 	
			}
			else
			{
				$attr->hydrate($val);
			} 
		}
	}
}
