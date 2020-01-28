<?php
include C_INC.'location_fonc.php'; 

define('NB_JOUR_VISIBLE', 120); 
define('TPS_CACHE', 600 ); 

//Récupération du code 
$code = ( isset( $_GET['c'] ) ) ? (int)$_GET['c'] : 0; 

if(!empty($code) )
{
	$donne = req('SELECT * FROM Externe WHERE code='.(int)$code .' LIMIT 1 '); 

	if($do = fetch($donne) ) 
	{
		$id_externe = (int)$do['id'];
	}
	else
	{
		exit(); 
	}
}
else
{
	exit(); 	
}

// Statistique 
location_stat_rss($id_externe); 

//Création des paramètres 
$nbparpage = 50; 

/*
	MISE EN CACHE 
*/

$id_cache = cache_id($code,'rss_ext' ); 

if(cache($id_cache, TPS_CACHE) )
{
	//Traitement sur les dates 
	$realdate = $datepast = ''; 
	mes_date(date('Y-m-d') , NB_JOUR_VISIBLE, $realdate, $datepast);

	// Evenements
	$lsevent = new ls_evenement( array(
		'champ' => EVCH_DATE|EVCH_LIEU|EVCH_DESC|EVCH_CONTACT, 
		'fi_date_min' => $realdate,
		'fi_date_max' => $datepast,
		'fi_id_externe' => $id_externe,
		'fi_str_actif' => FALSE
	) ); 

	$lsevent->mut_nb_par_page($nbparpage); 
	$lsevent->mut_mode(reqo::LIMITE); 
	$lsevent->requete(); 
//	imp($lsevent);
//	exit(); 

	// Patron 
	$PAT->mvar('lng', 'fr-fr'); 
	$PAT->mvar('description', $lang['desclong'] );
	$baseurl = empty($do['lien_rss']) ? ADD_SITE.'page/autre-date.php?id=%id' : secuhtml($do['lien_rss']); 
	$PAT->mut_titre($do['titre_flux'] ? $do['nom'] : $lang['Title']); 

	$PAT->mut_type(patron::RSS); 
	$PAT->def_patron();
	$PAT->ajt_patron('rss.p.php'); 

	include PATRON;  
}
header('Content-type: application/rss+xml');
cache(); 
