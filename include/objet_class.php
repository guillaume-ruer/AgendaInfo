<?php

class objet 
{
	function __construct($do=array() )
	{
		$this->hydrate($do); 
	}

	function hydrate( $do=array() )
	{
		if( !is_array($do) )
		{
			$this->erreur("L'argument doit être un tableau."); 
		}
		else
		{
			foreach( $do as $nom => $val )
			{
				$this->__set($nom, $val); 
			}
		}
	}

	function erreur($var)
	{
		if( MODE_DEV )
		{
			trigger_error("\n\n".$var."\n\n"); 
		}
	}

	function meta_name($name)
	{
		$class = get_class($this); 
		return isset($class::${'m_'.$name}) ? $class::${'m_'.$name} : array(); 
	}

	function aff()
	{
		echo '<pre>';
		var_dump($this); 
		echo '</pre>';
	}

	function ajt_element($name, $value)
	{
		$opt = $this->meta_name($name); 

		if( isset($opt['max']) && (count($this->$name) >= $opt['max']) )
		{
			$this->erreur('Limite d\'élément atteints ('.$opt['max'].') pour "'.$name.'"'); 
			return FALSE; 
		}

		if( isset($opt['type']) && is_string($opt['type']) && 
			strpos($opt['type'], '__') === 0 )
		{
			$obj = substr($opt['type'], 2); 

			if( is_array($value) )
			{
				$tmp = new $obj($value); 	
			}
			elseif( $value instanceof $obj )
			{
				$tmp = $value; 
			}
			else
			{
				$this->erreur("Objet de type $obj attendu pour le tableau $name, ".
					"\"".gettype($value)."\" donnée. "); 
				return FALSE; 
			}
		}
		elseif( isset($opt['filtre']) && is_callable($opt['filtre']) )
		{
			$tmp = $opt['filtre']($v); 	
		}
		else
		{
			$tmp = $value; 
		}

		$this->{$name}[] = $tmp; 
		return TRUE; 
	}

	function __set($name, $value )
	{
		if( method_exists($this, 'mut_'.$name) )
		{
			$this->{'mut_'.$name}($value); 
		}
		elseif( property_exists($this, $name) )
		{
			$opt = $this->meta_name($name); 

			if( is_object($this->$name) )
			{
				if( is_null($value) )
				{
					$class = get_class($this->$name); 
					$this->$name = '__'.$class; 
				}
				elseif( is_object($value) )
				{
					if( $value instanceof $this->$name )
					{
						$this->$name = $value; 
					}
				}
				elseif( is_array($value) )
				{
					$this->$name->hydrate($value); 
				}
				else
				{
					$this->erreur('Type de valeur invalide'); 
				}
			}
			elseif( is_array($this->$name) )
			{
				if( is_array($value) )
				{
					$this->$name = array(); 

					foreach($value as $v )
					{
						$this->ajt_element($name, $v); 
					}
				}
				elseif( is_string($value) )
				{
					$this->$name = array(); 
					if( $tmp = unserialize($value) )
					{
						$this->$name = $tmp; 
					}
				}
			}
			elseif( strpos($this->$name, '__') === 0 )
			{
				$obj = substr($this->$name, 2); 
				$this->$name = new $obj; 

				if( is_object($value) && ($value instanceof $this->$name) )
				{
					$this->$name = $value; 		
				}
				elseif( is_array($value) ) 
				{
					$this->$name->hydrate($value); 
				}
				else
				{
					$this->erreur('Valeur invalide : '.$value); 
				}
			}
			elseif( is_float($this->$name) )
			{
				$this->$name = (float)str_replace(',','.', $value); 
			}
			else
			{
				settype($value, gettype($this->$name) );
				$this->$name = $value; 

				if(is_string($this->$name) && isset($opt['max']) )
				{
					$this->$name = substr($this->$name, 0, $opt['max']); 
				}
			}
		}
		else
		{
			$this->erreur('Nom de propriété inexistante : '.$name); 
		}
	}

	function __get($name)
	{
		if( method_exists($this, 'aff_'.$name) )
		{
			$this->{'aff_'.$name}(); 
		}
		elseif( property_exists($this, $name) )
		{
			$champ = $this->$name; 
			$opt = $this->meta_name($name); 

			if( is_string($champ) )
			{
				if( isset($opt['brute']) && $opt['brute'] == TRUE )
				{
					echo $champ; 
				}
				else
				{
					ps($champ); 
				}
			}
			elseif( is_object($champ) )
			{
				$champ->aff(); 
			}
			else
			{
				echo $champ; 
			}
		}
		else
		{
			$this->erreur('Nom de propriété inexistante : '.$name); 
		}
	}

	function __call($name, $arg)
	{
		if( method_exists($this, 'acc_'.$name) )
		{
			return $this->{'acc_'.$name}(); 
		}
		elseif( property_exists($this, $name) )
		{
			if( count($arg) == 0 )
			{
				if( is_string($this->$name) && (strpos($this->$name, '__') === 0 ) )
				{
					$obj = substr($this->$name, 2); 
					$this->$name = new $obj; 
				}

				return $this->$name; 
			}
			else
			{
				$this->erreur('Trop d\'argument pour la méthode '.$name.' '); 
			}
		}
		else
		{
			$call = $name; 
			$met = substr($name, 0, 4); 
			$name = substr($name, 4); 

			switch($met)
			{
				case 'aff_' : 
					$this->__get($name); 	
				break;
				case 'acc_' : 
					if( is_string($this->$name) && (strpos($this->$name, '__') === 0 ) )
					{
						$obj = substr($this->$name, 2); 
						$this->$name = new $obj; 
					}

					return $this->$name; 
				break;
				case 'mut_' : 
					$this->__set($name, $arg[0]); 
				break; 
				case 'ajt_' : 
					if( is_array($this->$name) )
					{
						$this->ajt_element($name, $arg[0]); 
					}
					else
					{
						$this->erreur('Ne peux ajouter des éléments qu\'à des tableaux.'); 
					}
				break;
				default: 
					$this->erreur('Nom de méthode non pris en charge : '.$call.' '); 
			}
		}
	}


}

