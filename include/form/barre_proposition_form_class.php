<?php
require_once C_INC.'form/champ_form_class.php'; 

class barre_proposition_form extends champ_form
{
	protected $name='barre_proposition'; 
	protected $fichier=''; 
	protected $class=''; 
	protected $limite=NULL; 
	protected $size=20; 

	function __construct( $do=array() )
	{
		global $PAT; 
		parent::__construct($do); 
		$PAT->ajt_script('jquery.js');
		$PAT->ajt_style('bp-style.css'); 
		$PAT->ajt_script('bp.js'); 
		ajt_script('fonc.js');
		ajt_script('jquery.js');
		ajt_style('bp-style.css'); 
		ajt_script('bp.js'); 
	}

	function mut_limite($lim)
	{
		$this->limite = empty($lim) ? NULL : (int)$lim; 
	}

	function mut_donne($donne)
	{ 
		if( $donne instanceof proposition )
		{
			$this->donne = array($donne);
		}
		elseif( is_array($donne) )
		{
			$this->donne = array(); 
			foreach( $donne as $do )
			{
				if( $do instanceof proposition )
				{
					$this->donne[] = $do; 
				}
				else
				{
					trigger_error('L\'élément doit implémenté l\'interface <proposition>'); 
				}
			}
		}
		else
		{
			trigger_error('Mauvais type de donnée, tableau de <proposition> ou une <proposition> attendu.'); 
		}
	}

	function acc_donne()
	{ 
		return (array)$this->donne; 
	}

	function verif()
	{
		return parent::verif(); 
	}

	function aff_champ()
	{
		$donne = $this->acc_donne(); 
		echo '<span class="bp_boite" data-bp-fichier="'.$this->acc_fichier().'" data-limite="'.$this->limite.'" >';
		echo '<span class="bp_relatif" >';

		echo '<input id="'.$this->acc_identifiant().'" type="text" name="'.$this->acc_nom_champ()
			.'" size="'.$this->size.'" value="" autocomplete="off" />';

		echo '<span class="bp_proposition" ></span>'; 
		echo '</span>'; 

		echo '<span class="bp_multiple" >'; 

		foreach($this->acc_donne() as $d ) 
		{
			echo '<span class="bp_choi form_ext_remove" >'; 
			echo $d->etiquette(); 
			echo '<input type="hidden" name="bp_id_'.$this->acc_nom_champ() .'[]" value="'.(int)$d->id().'" />'; 

			$bp_do_val = htmlspecialchars( html_entity_decode($d->json(), ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
			echo '<input type="hidden" name="bp_do_'.$this->acc_nom_champ() .'[]" value="'.$bp_do_val.'" />'; 
			echo '<a class="bp_sup" >X</a>'; 
			echo '</span>'; 
		}
		echo '</span>'; 

		echo '</span>'; 
	}

	function recup()
	{
		if( is_null($this->indice) )
		{
			list($id, $nom) = http_param_tab( array('bp_id_'.$this->acc_nom() => array(),'bp_do_'.$this->acc_nom() => array() ) ); 
			$id = array_unique( array_map('intval', $id) ); 
		}
		else
		{
			$id = isset($_POST['bp_id_'.$this->acc_nom()][$this->indice] ) ? 
				array_unique( array_map('intval', $_POST['bp_id_'.$this->acc_nom()][$this->indice])) : array(); 
			$nom = isset($_POST['bp_do_'.$this->acc_nom()][$this->indice] ) ? 
				$_POST['bp_do_'.$this->acc_nom()][$this->indice] : array(); 
		}

		$res = array(); 
		$nb=0; 
		foreach( $id as $cle => $i )
		{
			if( !is_null($this->limite) && ($nb==$this->limite) )
				break; 
			debug($nom[$cle]); 
			debug(json_decode($nom[$cle]) ); 
			debug(json_decode(html_entity_decode($nom[$cle]) ) ); 
			$c = new $this->class( genere_init( json_decode( html_entity_decode($nom[$cle], ENT_QUOTES ), TRUE ) ) );
			$c->id = $i; 
			$res[] = $c; 
			$nb++; 
		}

		$this->donne = $res; 
	}
}
