<?php
include C_INC.'location_fonc.php'; 

define('NB_JOUR_VISIBLE', 120 ); 
define('NB_JOUR_CALENDRIER', 60 );
define('TPS_CACHE', MODE_DEV ? 0 : 3600); 

//Récupération du code 
$code = isset($_GET['c']) ? (int)$_GET['c'] : (isset($_POST['c']) ? (int)$_POST['c'] : 0 ); 

if(!empty($code) )
{
	$donne = req('SELECT * FROM Externe WHERE code='.(int)$code .' LIMIT 1 '); 
	$do = fetch($donne);

	if(empty($do) ) 
		exit(); 
}
else
{
	exit(); 	
}


/*
	Récupération des entrée utilisateur 
*/

if(isset($_POST['ok']) )
{
	/*
		Récupération post 
	*/
	$page = 0; 
	$theme = (isset($_POST['theme']) ) ? noui($_POST['theme']) : NULL; 
	$input_date = (isset($_POST['DateDeb']) ) ? date_format_traitement($_POST['DateDeb']) : date('Y-m-d') ; 
	$langue = (isset($_POST['l']) ) ? (int) $_POST['l'] : 1; 
}
else
{
	/*
		Récupération get 
	*/
	$page = (isset($_GET['pg'] ) ) ? (int)$_GET['pg'] : 0; 
	$theme = (isset($_GET['idt']) ) ? noui($_GET['idt']) : NULL ; 
	$input_date = (isset($_GET['d']) ) ? $_GET['d'] : date('Y-m-d'); 
	$langue = (isset($_GET['l']) ) ? (int) $_GET['l'] : 1; 
}

//Création des paramètres 
$id_externe = (int)$do['id'];
$filtre_externe = absint($do['filtre']); 
$nbparpage = 20; 
$lieu = ''; 
$groupe_lieu =''; 

// Statistique 
location_stat_ext($do); 

//Traitement sur les dates 
$realdate = $datepast = ''; 
mes_date($input_date, NB_JOUR_VISIBLE, $realdate, $datepast);

/*
	MISE EN CACHE 
*/
$id_cache = cache_id( $code, $id_externe,$realdate, $theme, $realdate, $page, 'ext'  ); 
if(cache($id_cache, TPS_CACHE ) )
{
	//Evenements
	$lsevent = new ls_evenement( array(
		'champ' => EVCH_DATE|EVCH_CAT|EVCH_DESC|EVCH_NB_DATE|EVCH_CONTACT|EVCH_TARIF|EVCH_LIEU,
		'fi_date_min' => $realdate,
		'fi_date_max' => $datepast,
		'fi_grp_lieu' => $groupe_lieu, 
		'fi_theme' => $theme, 
		'fi_str_actif' => TRUE,
		'fi_id_externe' => $id_externe, 
	) ); 

	$lsevent->acc_pagin()->mut_avec_p(FALSE); 
	$lsevent->acc_pagin()->mut_url( url_externe($realdate, $code, $theme ) ); 
	$lsevent->acc_pagin()->mut_num_page( $page ); 
	$lsevent->mut_nb_par_page($nbparpage); 

	$lsevent->requete(); 

	//Css spécial : 
	$css_externe = nom_template($code); 
	$css_externe = ( empty( $css_externe ) ) ? 'base' : $css_externe;  

	/*
		Exception pour le template correzetv.rss 
	*/
	$PAT->def_patron(); 

	if(strpos( $css_externe , 'rss' ) )
	{
		$PAT->mut_type(patron::RSS); 
		$PAT->mvar('lng', 'fr-fr');
		$PAT->mvar('description', $lang['desclong']); 
		$PAT->mvar('baseurl', ADD_SITE ); 
		$PAT->mut_titre($lang['Title']);
		
		if($css_externe == 'rad.rss' )
		{
			$PAT->ajt_patron('rss_externe_rad.p.php'); 
		}
		else
		{
			$PAT->ajt_patron('rss_externe.p.php'); 
		}
	}
	else
	{

		//Pour le calendrier js
		$date_champ = date_format_fr_slashes($realdate); 
		$date_affiche = date_format_fr(date('Y-m-d') ); 
		$jsdate = date('Y,m,d',mktime(0, 0, 0, date('m')-1, date('d')+NB_JOUR_CALENDRIER, date('y'))); 
		$jsdate = explode(',' ,$jsdate );
		$js_jr = $jsdate[2];
		$js_moi = $jsdate[1];
		$js_annee = $jsdate[0];

		//Menu déroulant pour le filtrage par theme
		$where = ''; 
		if( $filtre_externe & LOC_THEME )
		{
			$where = ' WHERE id IN( SELECT id_theme FROM externe_theme WHERE id_externe='.$id_externe.' )'; 
		}

		$tab_theme = new reqa('SELECT absint::id, secuhtml::nom_fr nom FROM categories_grp '.$where);

		//Drapeau 
		$url_drapeau_fr = ADD_SITE.url_externe($page, $realdate, $code, $theme, 1);
		$url_drapeau_en = ADD_SITE.url_externe($page, $realdate, $code , $theme, 2); 

		$PAT->def_patron(); 
		$PAT->ajt_patron('externe.p.php', C_PATRON ); 


		$PAT->def_style(); 
		if( !empty($css_externe) ){ $PAT->ajt_style('style_'.$css_externe.'.css'); }
		$PAT->ajt_style('style_externe.css'); 
		$PAT->ajt_style('calendar-win2k-cold-1.css', RETOUR.'jscalendar/'); 

		$calendar = (ID_LANGUE == 2 ) ? 'calendar-en.js' : 'calendar-fr.js' ;
		$PAT->ajt_script('calendar.js', RETOUR.'jscalendar/' );
		$PAT->ajt_script('lang/'.$calendar, RETOUR.'jscalendar/' );
		$PAT->ajt_script('calendar-setup.js', RETOUR.'jscalendar/' );

		$url_rss = ADD_SITE.'externe/'.$code.'/0_0_FR.rss'; 

		$PAT->ajt_link(array(
			'rel'=>'alternate',
			'type' =>'application/rss+xml', 
			'title' => 'Flux rss', 
			'href' => $url_rss, 
		) );
		
		$url_pdf = ADD_SITE.'externe/pdf.php?c='.$code.'&idt='.$theme.'&date='.$realdate; 
	}

	include PATRON; 
}
cache(); 
