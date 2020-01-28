<?php

class tab_form extends base_form
{
	protected $prems=TRUE; 
	protected $type; 
	protected $tab_message; 
	protected $max=10; 
	protected $label=''; 

	function __construct($do=array() )
	{
		parent::__construct($do); 
		ajt_script('jquery.js'); 
		ajt_script('form-ext.js');
		ajt_style('form-ext.css'); 
	}

	function mut_donne($donne)
	{
		$this->donne = $donne; 
	}

	function verif()
	{
		$donne = $this->donne(); 
		$obj = $this->type; 
		$v = TRUE; 


		foreach($donne as $i => $do )
		{
			$obj->mut_donne($do); 
			$obj->mut_import(FALSE); 
			$obj->init_message(); 

			if( !$obj->verif() )
			{
				$v = FALSE; 
				
				if( isset($this->tab_message[$i]) )
				{
					$obj->message_fusion( $this->tab_message[$i] ); 
					$this->tab_message[$i] = $obj->acc_message(); 
				}
				else
				{
					$this->tab_message[$i] = $obj->acc_message(); 
				}
			}
		}

		return $v; 
	}

	function message_fusion($message)
	{
		$obj = $this->type; 

		foreach($message as $i => $mess )
		{
			if( isset($this->tab_message[$i]) )
			{
				$obj->init_message(); 
				$obj->message_fusion( $this->tab_message[$i] ); 
				$obj->message_fusion( $mess ); 
				$this->tab_message[$i] = $obj->acc_message(); 
			}
			else
			{
				$this->tab_message[$i] = $mess; 
			}
		}
	}

	function init_message()
	{
		$this->tab_message = array(); 
	}

	function acc_message()
	{
		return $this->tab_message; 
	}

	function ajt_message_class($class)
	{
		/*Non implémenté*/
	}

	function mut_indice($indice)
	{
		$this->type->mut_indice($indice); 	
	}

	function mut_max($max){ $this->max = noui($max); } 

	function mut_type($type)
	{
		if( $type instanceof base_form)
		{
			$this->type = $type; 
		}
		elseif( is_string($type) )
		{
			$c = $type.'_form'; 
			$this->type = new $c; 
		}
		else
		{
			trigger_error('Ne peut initialiser le type.'); 
		}
		
		if( is_object($this->type) )
		{
			$this->type->ajt_message_class('form_ext_remove'); 
		}
	}

	function recup()
	{
		$this->donne = array(); 

		if( isset($_POST[$this->nom()]) )
		{
			$donne = $_POST[$this->nom()]; 
			$obj = $this->type; 				

			for($j=0; (list(, $i) = each($donne) ) && ($j<$this->max); $j++ )
			{
				$obj->init_message(); 
				$obj->mut_indice($i); 
				$obj->mut_import(TRUE); 
				$this->donne[$i] = $obj->donne();	
				$this->tab_message[$i] = $obj->acc_message(); 
			}
		}
	}

	function aff()
	{
		$obj = $this->type; 

		echo '<fieldset class="form_ext_block" id="'.$this->acc_identifiant().'" data-max="'.$this->max.'" ><legend>'.$this->label.'</legend>'."\n"; 
		echo '<div class="form_ext_prems" >'."\n"; 

		$c = ''; 
		if( is_null($this->donne) )
		{
			$donne = array(NULL);
			$c = $this->prems ? '' : ' form_ext_non_prems'; 
		}
		elseif( empty($this->donne) )
		{
			$donne = array(NULL); 
			$c = ' form_ext_non_prems'; 
		}
		else
		{
			$donne = $this->donne; 
		}

		$j=0; 
		foreach($donne as $i => $d )
		{
			if( !is_null($d) )
			{
				$obj->mut_donne($d);
			}

			$obj->mut_indice($j); 

			if( isset($this->tab_message[$i]) )
			{
				$obj->mut_message($this->tab_message[$i]); 
			}
			else
			{
				$obj->init_message(); 
			}

			echo '<div class="form_ext_fieldset'.$c.'" >',"\n",'<fieldset><legend>'.$obj->label().'</legend>'."\n"; 

			$obj->aff(); 

			echo '<p class="form_ext_supr" ><input class="form_ext_hidden" type="hidden" name="'.$this->nom().'[]" value="'.$j.'" />
				<button class="form_ext_sup" type="button" >Supprimer</button>'."\n".'</p>'; 
			echo "\n</fieldset>\n</div>\n"; 
			$j++; 
		}

		echo '
			</div>
			<div><button class="form_ext_button" type="button" >Ajouter un champ</button></div>
		</fieldset>
		'; 
	}
}
