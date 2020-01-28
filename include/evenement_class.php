<?php
require_once C_INC.'contact_class.php'; 
require_once C_INC.'categorie_class.php'; 
require_once C_INC.'tarif_class.php'; 
require_once C_INC.'public_class.php'; 

/*
	Class événement 

	methode :

		init($id) : initialise l'objet avec les donnée de la bdd en fonction de l'id. 
		sup($mode) : suppression de l'evenement (par défaut : met le champ Actif à 2 )
		insert() : insertion de l'objet en bdd. 

*/

class evenement extends identifiant
{
	//Donnée de base 
	private $titre = ''; 
	private $desc = ''; 
	private $etat = 0; 
	private $aleat = 0; 
	private $tarif = NULL;
	private $nb_date = 0; 
	private $type=self::NORMAL; 

	//Créateur  
	private $createur=NULL; 
	private $date_creation=''; 

	//Le dernier modifieur 
	private $modifieur=NULL; 
	private $date_modif=''; 

	//La liste des dates 
	private $tab_date = array(); 

	//La liste des lieux 
	private $tab_lieu = array(); 
	
	//Contact 
	private $contact=NULL; 

	//Catégorie 
	private $categorie=NULL; 

	// Import externe (LEI, SIRTAQUI)
	private $source = 0; 
	private $id_externe = 0; 

	// Image 
	private $image=''; 

	// Affiche 
	private $affiche=''; 
	private $affiche_url=''; 

	// Date modif pour le sirtaqui 
	private $date_maj = 0; 

	// Stat 
	private $nbp = 0; 

	//Constante 
	const IL = 0; 
	const LEI = 1; // CRT 
	const STQ = 2; // Sirtaqui 

	// Etat 
	const MASQUE = 0;
	const ACTIF = 1;  
	const SUPP = 2;  

	// Type 
	const AFFICHE=1;
	const NORMAL=0; 

	// Constante format tableau
	const OPT_TAB_DATE_FR=1; 

	static $TAB_ETAT=array( self::MASQUE => 'Masqué', self::ACTIF => 'Actif', self::SUPP => 'Supprimé' ); 

	static $TAB_SOURCE = [
		self::IL => ['nom' => 'Info-Limousin', 'nom_complet' => 'Info-Limousin'], 
		self::LEI => ['nom' => 'LEI', 'nom_complet' => 'LEI (CRT Limousin)'], 
		self::STQ => ['nom' => 'SIRTAQUI', 'nom_complet' => 'SIRTAQUI'], 
	];

	// Public 
	private $public=NULL; 

	/*
		Accesseurs 
	*/
	function acc_titre(){ return $this->titre; } 
	function acc_desc(){ return $this->desc; } 
	function acc_etat(){ return $this->etat; } 
	function acc_aleat(){ return $this->aleat; } 

	function acc_tarif()
	{ 
		if( is_null($this->tarif) )
		{
			$this->tarif = new tarif; 
		}

		return $this->tarif; 
	} 

	function acc_createur()
	{ 
		if( is_null($this->createur ) )
		{
			$this->createur = new id_nom; 
		}

		return $this->createur; 
	} 
	function acc_date_creation(){ return $this->date_creation; } 
	function acc_modifieur()
	{ 
		if( is_null($this->modifieur ) )
		{
			$this->modifieur = new id_nom; 
		}
		return $this->modifieur; 
	} 
	function acc_date_modif(){ return $this->date_modif; } 
	function acc_tab_date($id=NULL)
	{ 
		return is_null($id) || !isset($this->tab_date[$id]) ? $this->tab_date : $this->tab_date[$id]; 
	} 
	function acc_tab_lieu(){ return $this->tab_lieu; } 

	function acc_contact()
	{ 
		if( is_null($this->contact) )
		{
			$this->contact = new contact; 
		}

		return $this->contact; 
	} 

	function acc_categorie()
	{ 
		if( is_null($this->categorie) )
		{
			$this->categorie = new categorie; 
		}
		return $this->categorie; 
	} 
	function acc_source(){ return $this->source; } 
	function acc_id_externe(){ return $this->id_externe; } 
	function acc_nb_date(){ return $this->nb_date; } 
	function acc_lieu($num=0)
	{
		if( isset($this->tab_lieu[$num]) )
		{
			return $this->tab_lieu[$num]; 
		}
		else
		{
			$l = new ville; 
			return $l; 
		}
	}

	function acc_image(){ return $this->image; } 
	function aff_image_enclosure()
	{
		$url = ADD_SITE.C_EVENT_IMAGE.$this->image; 
		$size = filesize(C_EVENT_IMAGE.$this->image); 
		$mime = mime_content_type(C_EVENT_IMAGE.$this->image); 
		echo '<enclosure url="'.$url.'" length="'.$size.'" type="'.$mime.'" />'; 
	}

	function acc_public()
	{ 
		if( is_null($this->public) )
		{
			$this->public = new event_public; 
		}

		return $this->public; 
	} 


	function acc_affiche(){ return $this->affiche; } 
	function acc_affiche_url(){ return $this->affiche_url; } 
	function acc_type(){ return $this->type; }
	function acc_nbp(){ return $this->nbp; } 

	/*
		Mutateurs
	*/
	function mut_titre($t){ $this->titre = ucfirst( trim($t) ); } 
	function mut_desc($desc) { $this->desc = ucfirst( trim($desc) ); } 
	function mut_etat($e)
	{ 
		if( self::est_etat($e) )
		{
			$this->etat = $e; 
		}
	} 
	function mut_aleat($a){ $this->aleat = $a; } 

	function mut_tarif($t)
	{ 
		$this->crouh($this->tarif, $t, 'tarif'); 
	} 

	function mut_createur($c)
	{
		$this->crouh($this->createur, $c, 'id_nom' ); 
	}

	function acc_date_maj(){ return $this->date_maj; }
	function mut_date_maj($d){ $this->date_maj=(int)$d; } 

	function mut_date_creation($d){ $this->date_creation=$d; } 
	function mut_modifieur($m)
	{
		$this->crouh($this->modifieur, $m, 'id_nom'); 
	}

	function mut_date_modif($d){ $this->date_modif=$d; } 
	function mut_tab_date($d)
	{ 
		$v=FALSE; 
		if( is_array($d) )
		{
			$tab_date = $d; 
			$v = TRUE; 
		}
		elseif( is_string($d) )
		{
			$tab_date = explode(',', $d); 
			$v = TRUE; 
		}

		if( $v )
		{
			foreach($tab_date as $d )
			{
				$this->ajt_date($d); 
			}
		}
	}

	function ajt_date($d)
	{
		if( verif_date($d) )
		{
			$this->tab_date[] = $d; 
		}
	}

	/*
		Passer un tableau de lieu. 
		Ou une chaîne de type : 
		<id>:<nom>:<dep>[;<id>:<nom>:<dep>[...]]
		depuis laquelle sera extrait les informations des villes. 
	*/

	function mut_tab_lieu($lieu)
	{ 
		$this->tab_lieu=array(); 

		if( is_array($lieu) )
		{
			foreach($lieu as $l )
			{
				$this->ajt_lieu($l); 
			}
		}
		elseif( is_string($lieu) )
		{
			$tab = explode(';', $lieu ); 

			foreach( $tab as $v )
			{   
				list( $id, $nom, $dep, $lat, $lng ) = explode(':', $v); 
				$this->ajt_lieu( array(
					'id' => $id, 
					'nom'=> $nom, 
					'dep' => array('num' => $dep ),  
					'url' => url_ville($id, 0, ID_LANGUE, $nom ),
					'lat' => (float)$lat,
					'long' => (float)$lng
				) );  
			}  		
		}
	} 

	function ajt_lieu($l)
	{
		if( is_array($l) )
		{
			$this->tab_lieu[] = new ville($l); 
		}
		elseif( est_class($l, 'ville') )
		{
			$this->tab_lieu[] = $l; 
		}
	}

	function mut_contact($c)
	{
		$this->crouh($this->contact, $c, 'contact'); 
	}

	function mut_categorie($c)
	{
		$this->crouh($this->categorie, $c, 'categorie'); 
	}

	function mut_source($s)
	{
		$this->source = $s; 
	}

	function mut_id_externe($id){ $this->id_externe = $id; } 
	function mut_nb_date($nb){ $this->nb_date=$nb; } 

	function mut_image($image)
	{ 
		$this->image = $image; 
	} 

	function mut_public($public)
	{
		$this->crouh( $this->public, $public, 'event_public'); 
	}

	function mut_affiche($affiche)
	{
		$this->affiche = $affiche; 
		$this->type(); 
	}

	function mut_affiche_url($affiche_url)
	{
		$this->affiche_url = lien($affiche_url); 
		$this->type(); 
	}

	function mut_nbp($nbp){ $this->nbp = (int)$nbp; } 

	/*
		Afficheurs 
	*/

	function aff_titre($long=NULL)
	{
		echo secuhtml( !is_null($long) && strlen($this->titre) > $long 
			? utf8_encode(substr(utf8_decode($this->titre), 0, $long ) ).'...' 
			: $this->titre 
		) ; 
	}

	function aff_url_autre_date()
	{
		echo ( $this->nb_date > 1 ) ? ADD_SITE.'page/autre-date.php?id='.$this->acc_id().'&amp;l='.ID_LANGUE : '';
	}

	function aff_date_modif()
	{
		echo date('Y-m-d G:i:s', absint($this->date_modif) );
	}

	function aff_date($id=0)
	{
		echo isset($this->tab_date[$id]) ? date_format_fr($this->tab_date[$id]) : '';  
	}

	function aff_desc($lien=TRUE)
	{
		echo $lien ? lien_text(secuhtml($this->desc) ) : secuhtml($this->desc); 
	}

	function aff_source()
	{
		if( $this->source != self::IL )
		{
			echo '(source '.self::$TAB_SOURCE[$this->source]['nom'].')'; 
		}
	}

	function aff_etat()
	{
		echo self::$TAB_ETAT[ $this->etat ]; 	
	}

	static function aff_source_nom($s)
	{
		echo self::acc_source_nom($s); 
	}

	static function acc_source_nom($s)
	{
		return self::$TAB_SOURCE[$s]['nom'];
	}

	static function aff_source_nom_complet($s)
	{
		echo self::acc_source_nom_complet($s); 
	}

	static function acc_source_nom_complet($s)
	{
		return self::$TAB_SOURCE[$s]['nom_complet'];
	}



	/*
		Autre opérations
	*/

	function a_lieu($id)
	{
		foreach($this->tab_lieu as $l )
		{
			if( $l->acc_id() == $id )
			{
				return TRUE; 
			}
		}

		return FALSE; 
	}

	function modif($mod)
	{
		return ($mod & $this->modif); 
	}

	static function est_etat($e)
	{
		return  in_array($e, array(self::MASQUE, self::ACTIF, self::SUPP ) ); 
	}

	function type()
	{
		$this->type = empty($this->affiche) || empty($this->affiche_url) ? self::NORMAL : self::AFFICHE; 
	}

	function div_contact()
	{
		$r = secuhtml($this->acc_contact()->acc_structure()->acc_nom() );
		$r .= ' '.secuhtml( $this->acc_contact()->acc_titre() );

		if( $this->source != self::IL)
		{
			$dm = ''; 
			if( $this->source == self::STQ && !empty($this->date_maj) )
			{
				$dm = ' - <span style="font-size:90%" >mise à jour : '.date('d/m/Y', $this->date_maj).'</span>'; 	
			}

			$r .=' (source '.self::$TAB_SOURCE[$this->source]['nom'].$dm.')'; 
		}

		$r .= ' '.secuhtml( $this->acc_contact()->acc_tel() );

		if( $this->acc_contact()->acc_site() != '' ) 
		{
			$r .= ' [<a target="_blank" href="'.$this->acc_contact()->acc_site().'" >'.$this->acc_contact()->acc_site().'</a>]'; 
		}

		return $r; 
	}

	function date_planning($ddu, $dau)
	{
		foreach($this->tab_date as $date)
		{
			$d = strtotime($date); 
			if( $d >= $ddu && $d <= $dau )
			{
				return $date; 
			}
		}

		return $this->tab_date[0]; 
	}

	function planning_html($param)
	{

		$cat = $this->acc_categorie()->chaine(); 
		$desc = secuhtml($this->desc); 
		$titre = secuhtml($this->titre); 
		$contact = $this->div_contact(); 
		$id = $this->acc_id(); 
		$date = date_format_fr($this->date_planning($param['datedu'], $param['dateau']) );  

		$bt_date = count($this->tab_date) > 1 ? '<a class="voir_date" ></a> <a class="gestion_date" ></a>' : ''; 

		$ui = ''; 

		if( $this->acc_image() ) 
		{
			$ui = secuhtml( 'http://info-limousin.com/dos-php/event_image/'.$this->acc_image() );
		}
		elseif( $this->acc_contact()->acc_structure()->acc_logo() ) 
		{
			$ui= secuhtml('http://info-limousin.com/img/logos/'.$this->acc_contact()->acc_structure()->acc_logo() ); 
		}
		
		$visuel = !empty($ui) ? '<img class="ev-visuel" src="'.$ui.'" />' : '';  

		$ev = array(
			'id' => $this->acc_id(), 
			'date' => $this->acc_tab_date(),
			'categorie' => array(
				'groupe' => $this->acc_categorie()->acc_groupe(), 
			), 
		); 

		foreach($this->acc_tab_lieu() as $lieu) 
		{
			$ev['lieu'][] = array(
				'id' => $lieu->acc_id(), 
				'nom' => $lieu->acc_nom(),
				'dep' => $lieu->acc_dep()->acc_num(),
			); 
		}

		$lieu = implode(', ', array_map(function($d){ return $d->acc_nom();}, $this->tab_lieu) ); 

		$dataevent = secuhtml(JSON_encode($ev) ); 

		$html =<<<START
			
<div class="evenement" data-id="{$id}" data-num="" data-event="$dataevent" >
	$visuel
	<div class="event_cg" >
		$cat
		<a class="bt_ajouter" ></a>
		<strong class="event_num" ></strong>
		<a class="bt_retire" ></a>
	</div>
	<div class="event_contenu" >
		<h2>$date $bt_date - $titre - $lieu</h2>
		<p class="ev_desc" >$desc</p>
		<p class="ev_contact" >$contact</p>
	</div>

</div>
START;
		return $html; 
	}

	function tab($opt= self::OPT_TAB_DATE_FR)
	{
		$ui = ''; 
		if( $this->acc_image() ) 
		{
			$ui = secuhtml( 'http://info-limousin.com/dos-php/event_image/'.$this->acc_image() );
		}
		elseif( $this->acc_contact()->acc_structure()->acc_logo() ) 
		{
			$ui= secuhtml('http://info-limousin.com/img/logos/'.$this->acc_contact()->acc_structure()->acc_logo() ); 
		}

		$ev = array(
			'id' => $this->acc_id(), 
			'titre' => $this->acc_titre(), 
			'desc' => $this->acc_desc(), 
			'date' => $this->acc_tab_date(),
			'nb_date' => $this->acc_nb_date(), 
			'image' => $ui, 
		
			'categorie' => array(
				'id' => $this->acc_categorie()->acc_id(),
				'nom' => $this->acc_categorie()->acc_nom(),
				'img' => $this->acc_categorie()->acc_img(), 
				'groupe' => $this->acc_categorie()->acc_groupe(), 
				'groupe_nom' => $this->acc_categorie()->acc_groupe_nom(), 
				'width' => $this->acc_categorie()->acc_width(),
				'height' => $this->acc_categorie()->acc_height()
			), 
			'contact' => array(
				'id' => $this->acc_contact()->acc_id(),
				'titre' => $this->acc_contact()->acc_titre(),
				'tel' => $this->acc_contact()->acc_tel(),
				'site' => $this->acc_contact()->acc_site(),
				'structure' => array(
					'id' => $this->acc_contact()->acc_structure()->acc_id(),
					'nom' => $this->acc_contact()->acc_structure()->acc_nom(),
					'code_externe' => $this->acc_contact()->acc_structure()->acc_code_externe(),
				),
			)
		); 

		if($opt & self::OPT_TAB_DATE_FR)
		{
			$ev['date_format_fr'] = array_map('date_format_fr', $this->acc_tab_date() );
		}

		foreach($this->acc_tab_lieu() as $lieu) 
		{
			$ev['lieu'][] = array(
				'id' => $lieu->acc_id(), 
				'nom' => $lieu->acc_nom(),
				'dep' => $lieu->acc_dep()->acc_num(),
			); 
		}

		return $ev; 
	}

}

