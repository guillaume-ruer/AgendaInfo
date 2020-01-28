<?php
//Conf

define('TPS_CACHE', 600 ); 
define('NB_JOUR_VISIBLE', 120 );
$nbparpage = 50; 

//Récupération id du lieu / groupe de lieu 
$lieu = isset($_GET['idl']) ? noui($_GET['idl']) : NULL; 
$groupe_lieu = isset($_GET['idg']) ? noui($_GET['idg']) : NULL; 

//Récupération de l'id du theme 
$theme = isset($_GET['idt']) ? noui($_GET['idt']) : NULL; 
/*
	MISE EN CACHE 
*/
$id_cache = cache_id($theme, $lieu, $groupe_lieu, '-rss' );  

if(cache($id_cache, TPS_CACHE) )
{

	$realdate = $datepast = ''; 
	mes_date(date('Y-m-d'), NB_JOUR_VISIBLE, $realdate, $datepast);

	//Evenements
	$lsevent = new ls_evenement( array(
		'champ' => EVCH_DATE|EVCH_LIEU|EVCH_DESC|EVCH_CONTACT, 
		'fi_date_min' => $realdate,
		'fi_date_max' => $datepast,
		'fi_lieu' => $lieu,
		'fi_grp_lieu' => $groupe_lieu, 
		'fi_theme' => $theme, 
	) ); 

	$lsevent->mut_nb_par_page($nbparpage); 
	$lsevent->mut_mode(reqo::LIMITE); 
	$lsevent->requete(); 

	/*
		Variable à envoyer dans le code html 
	*/

	$PAT->mut_type(patron::RSS); 
	$PAT->mvar('lng','fr-fr');
	$baseurl = ADD_SITE.'page/autre-date.php?id=%id'; 
    $url_id = TRUE; 
	$PAT->mvar('description', $lang['desclong']);
	$PAT->mut_titre('Toute l\'info en Limousin :: '.info_filtre($realdate, $lieu, $groupe_lieu, $theme) ); 

	$PAT->def_patron(); 
	$PAT->ajt_patron('rss.p.php'); 

	include PATRON; 
}
header('Content-type: application/rss+xml');
cache(); 
