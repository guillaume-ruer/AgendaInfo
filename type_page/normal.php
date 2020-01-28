<?php
/*
	La page à afficher si on est sur le site. 
*/

include C_INC.'fonc_bandeau.php'; 
include C_INC.'visuel_liste_class.php';
include C_INC.'visuel_fonc.php'; 
include C_ADMIN.'include/visuel_conf.php'; 
require_once C_INC.'lien_grp_ls.php';
require_once C_INC.'lien_class.php'; 
require_once C_INC.'lien_ls.php';
require_once C_INC.'affichette_fonc.php'; 
require_once C_INC.'flux_rss_fonc.php'; 
require_once C_INC.'ville_fonc.php'; 

function retire_date($lien)
{
	return preg_replace('`[0-9]{1,2}\s*[a-z]{1,3}\s*[0-9]{1,4}\s*:?`i', '', $lien); 
}

function racourci_texte($texte, $l=100)
{
	$t = preg_replace('`<br\s*/?>`', ' ', $texte); 
	$t = strip_tags($t); 
	if( strlen($t) > $l )
	{
		$t = spesubstr($t, 0, $l).'...'; 
	}

	return $t; 
}

//Initialisation, definition... 
define('NB_JOUR_VISIBLE', 180);
define('NB_JOUR_CALENDRIER', 90 );
define('TPS_CACHE', MODE_DEV ? 0 : 3600); 
$nbparpage = 20; 


/*
	Récupération des entré utilisateur 
*/
$ch_lieu = new barre_proposition_form(['fichier'=> 'lieu.php', 'class' => 'ville', 'label' => 'ou lieu ', 'limite' => 1] ); 

if(isset($_POST['ok']) )
{
	/*
		Récupération POST 
	*/
	$input_date = (isset($_POST['DateDeb']) ) ? date_format_traitement($_POST['DateDeb']) : date('Y-m-d'); 

	$lieu = NULL; 

	if(isset($_POST['lieu']) )
	{
		$lieu = noui($_POST['lieu']);
	}
	
	$groupe_lieu = (isset($_POST['groupe_lieu']) ) ? noui($_POST['groupe_lieu']) : NULL; 
	$theme = (isset($_POST['theme']) ) ? noui($_POST['theme']) : NULL; 
	$page = 0;
	$langue = (isset($_POST['l']) ) ? (int) $_POST['l'] : 1; 

	if( $do_lieu = $ch_lieu->donne() )
	{
		$lieu = $do_lieu[0]->id(); 
	}
}
else
{
	/*
		Récupération GET  
	*/
	$input_date = (isset($_GET['date']) ) ? $_GET['date'] : date('Y-m-d'); 
	$lieu = (isset($_GET['idl']) ) ? noui($_GET['idl']) : NULL; 

	if( !empty($lieu) )
	{
		$ville = ville_init($lieu); 
		$ch_lieu->mut_donne($ville); 
	}
	
	$theme = (isset($_GET['idt']) ) ? noui($_GET['idt']) : NULL; 
	$page = (isset($_GET['pg']) ) ? (int)$_GET['pg'] : 0 ; 
	$groupe_lieu = (isset($_GET['gl']) ) ? noui($_GET['gl']) : NULL; 
	$langue = (isset($_GET['l']) ) ? (int) $_GET['l'] : 1; 
}

/*
	Traitement sur les dates 
*/
$realdate = $datepast = ''; 
mes_date($input_date, NB_JOUR_VISIBLE, $realdate, $datepast);

/*
	MISE EN CACHE 
*/

$id_cache= cache_id($input_date, $lieu, $theme, $page, $groupe_lieu, 'norm' );

if(cache($id_cache, TPS_CACHE  ) )
{

	$PAT->def_patron(); 
	$PAT->ajt_patron('bandeau.p.php', C_PATRON); 
	$PAT->ajt_patron('index.p.php', C_PATRON); 
	$PAT->ajt_patron('pied-page.php', C_PATRON); 

	$PAT->ajt_script('jquery.js');
	$PAT->ajt_script('redirection.js');

	// Calendrier
	$dos = RETOUR.'JSCal2/';
	$PAT->ajt_style('css/jscal2.css', $dos);
	$PAT->ajt_style('css/border-radius.css', $dos);

	$PAT->ajt_script('js/jscal2.js', $dos );  
	$PAT->ajt_script('js/lang/fr.js', $dos );  

	$PAT->ajt_style('fond.php'); 
	$PAT->ajt_style('style_index.css'); 

	$PAT->ajt_script('menu_deroulant.js');
	$PAT->ajt_script('lien_deroulant.js');
	
		//La petite phrase indiquant les éléments du filtrage
	$datedeb = date_format_fr($realdate); 
	$datefin = date_format_fr($datepast); 
	if(!empty($lieu) )
	{
		$do_lieu = nom_lieu($lieu); 
		$lieu_filtre = $do_lieu['nom_lieu'];
	}
	elseif(!empty($groupe_lieu ) )
	{
		$lieu_filtre = nom_groupe_lieu($groupe_lieu ); 
	}
	else
	{
		$lieu_filtre = ' Tout le limousin '; 
	}

	$nom_theme = (!empty($theme ) ) ? nom_theme($theme) : ' tous les thèmes ';

	$info_filtrage = $lieu_filtre . ' - à partir du ' . $datedeb . ' - ' . $nom_theme ;  
	$titre = 'Info Limousin :: '.$info_filtrage; 

	//Evenements
	$lsevent = new ls_evenement( array(
		'champ' => EVCH_DATE|EVCH_CAT|EVCH_LIEU|EVCH_DESC|EVCH_NB_DATE|EVCH_CONTACT|EVCH_TARIF, 
		'fi_date_min' => $realdate,
		'fi_date_max' => $datepast,
		'fi_lieu' => $lieu,
		'fi_grp_lieu' => $groupe_lieu, 
		'fi_theme' => $theme, 
		'fi_str_actif' => TRUE
	) ); 

	$lsevent->acc_pagin()->mut_url(url_normal($realdate, $lieu, $groupe_lieu, 
		$theme, NULL, ID_LANGUE, $lieu_filtre.','.$nom_theme) ); 
	$lsevent->acc_pagin()->mut_num_page($page); 
	$lsevent->acc_pagin()->mut_max_lien(20); 
	$lsevent->mut_nb_par_page($nbparpage); 

	$lsevent->requete(); 

	//Tableau pour les menu déroulant 
	$tab_groupe_lieu = opt_groupe_lieu($realdate, $datepast, $groupe_lieu, TRUE ); 
	$tab_lieu_spe = opt_lieu_spe($realdate, $datepast ); 
	$tab_theme = opt_theme($theme ); 

	//Dans le champs hidden pour la date 
	$date_champ = date_format_fr_slashes($realdate); 

	//Pour le calendrier js
	$js_max = date('Ymd',mktime(0, 0, 0, date('m'), date('d')+NB_JOUR_CALENDRIER, date('y'))); 

	//Drapeau 
	$url_drapeau_fr = ADD_SITE.url_normal($realdate, $lieu, $groupe_lieu, $theme, $page, 1 ) ; 
	$url_drapeau_en = ADD_SITE.url_normal( $realdate, $lieu, $groupe_lieu, $theme, $page, 2 ) ;

	//La langue du calendrier 
	$calendar = (ID_LANGUE == 2 ) ? 'calendar-en.js' : 'calendar-fr.js' ;

	// Affichette 
	$affichette = new ls_evenement( array(
		'fi_type' => evenement::AFFICHE,
		'mode' => reqo::LIMITE, 
		'nb_par_page' => 4,
		'champ' => EVCH_CONTACT|EVCH_DESC|EVCH_AFFICHE|EVCH_DATE,
		'order' => ls_evenement::ORDER_RAND, 
		'fi_str_actif' => FALSE, 
        'fi_date_max' => NULL
	) ); 

	$affichette->requete(); 

	//Url rss
	$rss = ADD_SITE.'feed/0_0_'; 
	$rss .= (ID_LANGUE == 2 ) ? 'EN' : 'FR' ;
	$rss .= (empty($groupe_lieu) ) ? '_'.$lieu : '_G'.$groupe_lieu ; 
	$rss .='___'; 
	$rss .= $theme; 
	$rss .= '_.rss'; 

	/*
		Flux limoges 

	$rss_limoge = flux_rss('http://www.ville-limoges.fr/index.php/fr/component/jevents/odandb.rss/rss/?format=feed&fullview=20');
	*/

        $lien_pdf = ADD_SITE.'externe/pdf.php?'; 
	$lien_pdf .= 'idl='.$lieu.'&amp;gl='.$groupe_lieu.'&amp;idt='.$theme.'&amp;d='.$input_date; 

	       
	/*
		Variable envoyer dans le code html 
	*/

	$date_affiche = date_format_fr(date('Y-m-d') ); 

	/*
		Configuration du patron 
	*/

	$PAT->ajt_link(array(
		'rel'=>'alternate',
		'type' =>'application/rss+xml', 
		'title' => 'Flux rss', 
		'href' => $rss, 
	)); 

	$PAT->ajt_meta('keywords', 'agenda, limousin, agenda limousin, agenda limoges, association info limousin, plateforme diffusion, tourisme limousin, actualite limousin, info limousin, plateau millevaches, flux rss, agenda haute-vienne, agenda creuse, agenda corrèze, vie association, '.$nom_theme); 
	$PAT->ajt_meta('description', "L'activité du Limousin au jour le jour : spectacles, rencontres sportives, horaires des marchés, sorties nature, brocantes, feux d'artifice, réunions associations, fêtes locales, animations enfants, soirées jeux... ");

	$PAT->mut_titre($titre); 

	//Le code html
	include PATRON;
}
cache(); 
memor_url(); 
