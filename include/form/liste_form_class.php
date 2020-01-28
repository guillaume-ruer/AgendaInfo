<?php

class liste_form extends base_form implements iterator
{
	protected $tab_champ = array(); 
	protected $liste_message; 

	function __construct($do=array() )
	{
		parent::__construct($do); 
		$this->liste_message = new message(); 
	}

	function ajt_mess($m, $class="message" )
	{
		$this->liste_message->ajt($m, $class); 
	}

	function aff_mess()
	{
		$this->liste_message->aff(); 
	}

	function acc($cle)
	{
		return isset($this->tab_champ[$cle]) ? $this->tab_champ[$cle] : FALSE; 
	}

	function message_fusion($message)
	{
		foreach($this->tab_champ as $cle => $champ )
		{
			$champ->message_fusion($message[$cle]); 
		}
	}

	function mut_donne($do)
	{
		if( is_null($do) )
		{
			$this->donne = array(); 

			foreach($this->tab_champ as $ch )
			{
				$ch->mut_donne(NULL); 
			}
		}
		elseif( is_array($do) )
		{
			$this->donne = $do; 

			foreach($do as $cle => $donne )
			{
				if( isset($this->tab_champ[$cle]) )
				{
					$this->tab_champ[ $cle ]->mut_donne($donne);
				}
				else
				{
					trigger_error('Champ inexistant : '.$cle); 
				}
			}
		}
		elseif( is_object($do) )
		{
			$this->donne = array(); 
			global $UTILISATEUR; 

			foreach( $this->tab_champ as $cle => $val )
			{
				if( property_exists( $do, $cle ) )
				{
					$met = 'acc_'.$cle; 
					$donne = $do->$met(); 
					$val->mut_donne($donne); 
					$this->donne[$cle] = $donne; 
				}
			}
		}
	}

	function init_message()
	{
		foreach($this->tab_champ as $cle => $ch)
		{
			$ch->init_message(); 
		}
	}

	function acc_message()
	{
		$res = array(); 
		foreach($this->tab_champ as $cle => $ch)
		{
			$res[$cle] = $ch->message(); 
		}

		return $res; 
	}

	function mut_message($tab)
	{
		foreach($tab as $cle => $val )
		{
			$this->tab_champ[$cle]->message = $val; 
		}
	}

	function ajt_message_class($class)
	{
		foreach($this->tab_champ as $val )
		{
			$val->ajt_message_class($class); 
		}
	}

	function mut_indice($indice)
	{
		$this->indice = $indice; 

		foreach($this->tab_champ as $ch)
		{
			$ch->mut_indice($indice); 
		}
	}

	function ajt($cle, $type, $label='', $option=array() )
	{
		if( is_object($type) )
		{
			if( $type instanceof base_form )
			{
				$this->tab_champ[$cle] = $type; 
			}
			else
			{
				trigger_error('ne peut ajouter le type de champ : '.get_class($type) ); 
			}
		}
		elseif( is_string($type) )
		{
			$type .= '_form';
			$this->tab_champ[$cle] = new $type( $option ); 
		}

		if( !empty($label) )
			$this->tab_champ[$cle]->mut_label($label); 

		return $this; 
	}

	function mut_nom($nom)
	{
		foreach($this->tab_champ as $cle => $ch)
		{
			$ch->mut_nom($nom.'_'.$cle); 
		}

		$this->nom = $nom; 
	}

	function mut_import($bool)
	{	
		foreach($this->tab_champ as $cle => $ch)
		{
			$ch->mut_import($bool); 
		}

		$this->import = $bool; 
	}

	function verif()
	{
		$v = TRUE; 

		foreach($this->tab_champ as $ch )
		{
			if( !$ch->verif() )
			{
				$v=FALSE; 
			}
		}
		
		if( $v )
		{
			$this->liste_message->ajt('Traitement de la demande effectué.', 'message_valide' );
		}
		else
		{
			$this->liste_message->ajt("Le formulaire n'a pas été validé, "
				."veuillez vérifier les champs comportant des messages d'erreurs.", 'message_erreur');
		}

		return $v; 
	}

	function aff()
	{
		foreach($this->tab_champ as $champ )
		{
			$champ->aff(); 
		}
	}


	function recup()
	{
		foreach($this->tab_champ as $cle => $champ )
		{
			$this->donne[$cle] = $champ->donne(); 
		}
	}

	function rewind()
	{
		reset($this->tab_champ); 
	}
	
	function next()
	{
		return next($this->tab_champ); 
	}

	function current()
	{
		return current($this->tab_champ); 
	}

	function key()
	{
		return key($this->tab_champ); 
	}

	function valid()
	{
		return current($this->tab_champ) !== FALSE; 
	}

}
