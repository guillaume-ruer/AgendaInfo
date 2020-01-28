<?php
require_once 'memor_fonc.php'; 

class fichier_form extends liste_form
{
	function __construct($do=array() )
	{
		$this->ajt('transfert', 'transfert', 'Fichier', isset($do['transfert']) ? $do['transfert'] : array() );
		$this->ajt('chaine', 'chaine', 'Nom', isset($do['chaine']) ? $do['chaine'] : array() );
		$this->ajt('texte', 'texte', 'Description', isset($do['texte']) ? $do['texte'] : array() ); 
		$this->ajt('cache', 'cache', isset($do['cache']) ? $do['cache'] : array() ); 
		unset($do['transfert'], $do['chaine'], $do['texte'], $do['cache']); 
		parent::__construct($do); 
	}

	function mut_donne($fichier)
	{
		if( $fichier instanceof fichier )
		{
			if( $fichier->src() != '')
			{
				$this->acc('transfert')->donne = $fichier; 
			}
			else
			{
				$this->acc('transfert')->donne = ''; 
			}

			$this->acc('chaine')->donne = $fichier->nom(); 
			$this->acc('texte')->donne = $fichier->description();
			$this->acc('cache')->donne = $fichier->id(); 
		}
		else
		{
			trigger_error('objet de type fichier attendu'); 
			debug($fichier); 
		}
	}

	function recup()
	{
		$this->donne = new fichier(); 
		$do = array(); 

		foreach($this->tab_champ as $cle => $val )
		{
			$do[$cle] = $val->donne(); 
		}

		if( $do['transfert'] !== FALSE )
		{
			$this->donne = $do['transfert']; 
		}

		$this->donne->nom = $do['chaine'];
		$this->donne->description = $do['texte']; 
		$this->donne->id = $do['cache'];
	}

	function aff()
	{
		echo '<div class="form_fichier_donne" >'; 
		$this->tab_champ['transfert']->aff(); 
		echo '</div>'; 
		echo '<div class="form_fichier_desc" >'; 
		$this->tab_champ['chaine']->aff(); 
		$this->tab_champ['cache']->aff(); 
		$this->tab_champ['texte']->aff(); 
		echo '</div>'; 
	}
}
