<?php

/*
	L'idée c'est de parcourir le flux xml du lei de la même manière que les jeux de résultats sql. 
	( ex : while( $tab_event = $flux->parcours() ) [...] )
	
	On intègre un filtre ( méthode : est_elimine ) qui fait qu'on saute directement à l'événement valide suivant. 
*/

class parcours_flux
{
	// Pour le fonctionnement de l'objet 
	private $xml=NULL;
	private $page=0; 
	private $etat = FALSE; 

	// Stats sur le fonctionnement 
	private $nbevent=0; 
	private $nbelim=0; 
	private $nberr =0; 

	private $flux_nb_event = 0; 

	// Valeur pouvant être paramêtré. 
	private $mois = 20; 
	private $locale = FALSE; // FALSE : en ligne, TRUE : en locale 
	private $modif = FALSE; // TRUE : modification d'événement aléatoire : FALSE : rien 
	private $limite = 10000; // Nombre d'événements maximum à traité avant l'arrêt
	private $ecart_ignore=0; // Ecart maximum entre deux date en secondes 

	const INTERVAL = 250; 
	const TEMPO = 6; 

	function __construct($locale=FALSE)
	{
		$this->ecart_ignore = 300 * 24 * 3600; 
		$this->xml = new XMLReader; 
		$this->mut_locale($locale); 
	}

	/*
		Retourne TRUE si on a réussi à se positionner sur une balise sit_liste après avoir ouvert 
		la prochaine page. 
		Retourne FALSE sinon.
	*/

	private function page_suivante()
	{
		$dos = C_DOS_PHP.'flux_lei/'; 
		$dest = $dos.'p'.$this->page.'.xml';

		if( !file_exists($dos) )
		{
			mkdir($dos);
		}

		if( !$this->locale )
		{
			sleep(self::TEMPO); 
			$tmp = $this->page * self::INTERVAL;
			$f = $tmp + 1 ; 
			$t = $tmp + self::INTERVAL;

			$fichier = 'http://www.tourisme-limousin.net/applications/xml/exploitation/listeproduits.asp'
				.'?rfrom='.$f.'&rto='.$t.'&user=2000033&pwkey=3a2b967c716113be7ecf4120501c031e&urlnames=tous&'
				.'PVALUES='.urlencode('30000006,@DJ,+'.$this->mois.'M')
				.'&PNAMES='.urlencode('elgendro,horariodu,horarioau')
				.'&clause=2000033000023' ; 
			copy($fichier,  $dest);  
		}

		$this->xml->open( $dest );  

		if( $this->page == 0 )
		{
			while($lecture = $this->xml->read() AND $this->xml->name != 'nbrecords' );
			$this->xml->read(); 
			$this->flux_nb_event = (int)$this->xml->value; 
		}

		$this->page++; 

		while($lecture = $this->xml->read() AND $this->xml->name != 'sit_liste' );
		return $lecture; 
	}

	/*
		Retourne -1 si le nœud ne peut être lu. 
		Retourne FALSE si il n'y a plus d'événements. 
		Retourne un tableau indicé si réussi. 
	*/

	private function suivant()
	{
		static $tab_cle = array('lieu', 'categorie', 'com', 'contact', 'date', 'cp', 'titre' ); 
		$donne = array(); 

		if($this->nbevent > $this->limite )
		{
			return FALSE; 
		}

		if( !$this->etat )
		{
			$this->etat = $this->page_suivante(); 
		}
		
		if( $this->etat )
		{
			$this->nbevent++; 
			if( ($node = $this->xml->expand() )=== FALSE )
			{
				$this->nberr++; 
				$this->etat = $this->xml->next('sit_liste'); 
				return -1;
			}

			$donne['id'] = contenu_element('PRODUIT', $node);
			$donne['titre'] = contenu_element('NOM', $node);
				
			$com1 = contenu_element('COMMENTAIRE', $node);
			$com2 = contenu_element('COMMENTAIREL1', $node);
			$donne['com'] = ($com1 == $com2 ) ? $com1 : $com1.$com2;

			//Inversion des description en bdd 

			if( !empty($com1) && !empty($com2) && $com1 != $com2 )
			{
				$donne['combdd'] = rtrim($com2, '.').'. '.$com1; 
			}
			else
			{
				if( !empty($com1) )
				{
					$donne['combdd'] = $com1; 
				}
				elseif( !empty($com2) )
				{
					$donne['combdd'] = $com2; 
				}
				else
				{
					$donne['combdd'] =''; 
				}
			}

			$donne['combdd'] = html_entity_decode($donne['combdd']);

			$donne['com1'] = $com1; 
			$donne['com2'] = $com2; 
					
			$donne['lieu'] = contenu_element('ADRPROD_LIBELLE_COMMUNE', $node);
			$donne['categorie'] = contenu_element('TYPE_NOM', $node);
			$donne['contact'] = contenu_element('ENTITE_GESTIONNAIRE', $node );
			$donne['adrprod_tel'] = contenu_element('ADRPROD_TEL', $node); 
			$donne['adrprod_url'] = contenu_element('ADRPROD_URL', $node); 
			$donne['adrprod_compl_adresse'] = contenu_element('ADRPROD_COMPL_ADRESSE', $node); 
			$donne['adrpec_compl_adresse'] = contenu_element('ADRPEC_COMPL_ADRESSE', $node); 
			$donne['date'] = date_duau($node);
			$donne['ct_nom'] = prod_nom($node); 
			$donne['ct_prenom'] = prod_prenom($node); 
			$donne['ct_tel'] = prod_tel($node);
			$donne['ct_site'] = prod_site($node); 
			$donne['cp'] = contenu_element('ADRPROD_CP', $node ); 

			if( $this->modif && (rand(0,100) > 90) )
			{
				foreach($tab_cle as $cle )
				{
					if( rand(0, count($tab_cle) ) == 1 )
					{
						if( $cle == 'date' )
						{
							$donne['date'] = array(
								array('du' => date('Y-m-d'), 'au' => date('Y-m-d', time() + 8*24*3600) )
							); 
						}
						elseif( $cle != 'contact' )
						{
							if( is_numeric($donne[$cle]) )
							{
								$donne[$cle] = rand(1,999); 
							}
							else
							{
								$donne[$cle] = '+'.$donne[$cle].'+';
							}
						}
					}
				}
			}

			$this->etat = $this->xml->next('sit_liste'); 
			return $donne; 
		}
		else
		{
			return FALSE; 
		}
	}

	/*
		Retourne les données du prochain événement valide dans un tableau indicé. 
		FALSE sinon. 
	*/

	public function parcours()
	{
		while( ($do = $this->suivant() ) && ( ($do==-1) || $this->est_elimine($do) ) );
		return $do; 
	}

	/*
		Retourne TRUE si l'événement est filtré.
		Retourne FALSE sinon. 
	*/

	private function est_elimine($do)
	{
		if( !dans_limousin($do['cp']) || est_visite_guide($do['categorie']) || empty($do['com']) ||
			date_duau_elim($do['date'], $this->ecart_ignore ) 
			|| empty($do['titre']) || empty($do['lieu']) )
		{
			$this->nbelim++;
			return TRUE; 
		}
		return FALSE; 
	}

	/*
		Mutateurs
	*/

	function mut_locale($var)
	{
		$this->locale = (bool)$var; 
	}

	function mut_limite($lim)
	{
		$this->limite = (int)$lim;
	}

	function mut_ecart_ignore($e)
	{
		$this->ecart_ignore = $e; 
	}

	function mut_mois($m){ $this->mois = $m; } 
	function mut_modif($m){ $this->modif = (bool)$m; }

	/*
		Accesseurs 
	*/

	function acc_nbelim()
	{
		return $this->nbelim;
	}

	function acc_flux_nb_event() 
	{
		return $this->flux_nb_event;
	}

	function acc_nberr()
	{
		return $this->nberr;
	}

	function acc_nbevent()
	{
		return $this->nbevent; 
	}
	
	function acc_mois(){ return $this->mois; } 

	function acc_modif(){ return $this->modif; } 
}
