<?php 

class onglet_form extends liste_form
{	
	private $vide=FALSE; 
	private $vide_message='Pas de formulaire'; 
	private $vide_titre='Aucun'; 

	function __construct($do=array())
	{
		global $PAT; 
		parent::__construct($do); 
		$PAT->ajt_script('onglet.js'); 
		$PAT->ajt_style('onglet.css'); 
	}

	function mut_defaut($defaut){ $this->donne['onglet'] = (string)$defaut; } 
	function mut_vide($vide){ $this->vide = (bool)$vide; } 
	function mut_vide_message($vide_message){ $this->vide_message = (string)$vide_message; } 
	function mut_vide_titre($vide_titre){ $this->vide_titre = (string)$vide_titre; } 

	function ajt_onglet($cle, $val ) 
	{
		parent::ajt($cle, $val); 
	}

	function recup()
	{
		if( is_null($this->indice) )
		{
			$nom = $_POST[$this->nom()]; 
		}
		else
		{
			$nom = $_POST[$this->nom()][$this->indice]; 	
		}

		if( isset( $this->tab_champ[$nom] ) )
		{
			$this->donne = array( 
				'onglet' => $nom,
				'donne' => $this->tab_champ[$nom]->donne() 
			); 
		}
	}

	function mut_donne($donne)
	{
		if( isset($donne['onglet'], $donne['donne']) )
		{
			if( isset($this->tab_champ[$donne['onglet'] ] ) )
			{
				foreach( $this->tab_champ as $cle => $ch )
				{
					if( $cle == $donne['onglet'] )
					{
						$ch->mut_donne( $donne['donne'] );
					}
					else
					{
						$ch->mut_donne(NULL);
					}
				}

				$this->donne = $donne; 
			}
			else
			{
				trigger_error('Champ '.$donne['onglet'].' inexistant.'); 
			}
		}
		else
		{
			trigger_error('Mauvais format de donnée, array("onglet" => string, "donne" => mixed) attendu.'); 
		}
	}

	function mut_message($message)
	{
		return $this->tab_champ[ $this->donne['onglet'] ]->mut_message($message); 
	}

	function message_fusion($message)
	{
		return $this->tab_champ[$this->donne['onglet'] ]->message_fusion($message); 
	}

	function acc_message()
	{
		return $this->tab_champ[$this->donne['onglet'] ]->acc_message(); 
	}

	function verif()
	{
		$donne = $this->donne(); 	
		return $this->tab_champ[$donne['onglet'] ]->verif(); 
	}

	function aff()
	{
		$tab_champ = $this->tab_champ; 
		$onglet = $this; 
		$defaut = $this->donne['onglet'] ; 
		$vide = $this->vide; 
		$vide_message = $this->vide_message; 
		$vide_titre= $this->vide_titre; 
		require C_FORM_PAT.'onglet_p.php'; 
	}
}
