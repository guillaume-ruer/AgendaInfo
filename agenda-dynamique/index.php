<?php
require '../include/init.php'; 
require '../type_page/include/init_module.php'; 
require C_INC.'planning_fonc.php'; 
require_once C_INC.'visuel_fonc.php'; 
require_once C_ADMIN.'include/visuel_conf.php'; 

visuel_const($VISUEL_CONF); 

define('RECUP_TOUT', 0); 
define('RECUP_EVENT', 1); 
define('RECUP_REM', 2); 

define('PL_LIMITE', 20);
define('TPS_CACHE', MODE_DEV ? 0 : 1800); 
define('NB_JOUR_VISIBLE', 120); 

http_param(['datedu'=>date('d-m-Y'), 'dateau' => NULL, 'np' => 1, 'x' => 0]); 

if( est_date($datedu) )
{
	$datedu = date_en2fr($datedu); 
}
elseif( !est_date_fr($datedu) )
{
	$datedu = date('d-m-Y'); 
}

if( est_date($dateau) )
{
	$dateau = date_en2fr($dateau); 
}
elseif( !est_date_fr($dateau) || is_null($dateau) )
{
	$dateau = $datedu; 
}

$num_page = $np-1;

$datedufr = $datedu; 
$dateaufr = $dateau; 
$dateau = date_fr2en($dateau); 
$datedu = date_fr2en($datedu); 

$lieu = (isset($_POST['idl']) ) ? noui($_POST['idl']) : NULL;
$rayon = (isset($_POST['ray']) ) ? noui($_POST['ray']) : NULL;

$theme = NULL; 
if( isset($_POST['idt']) )
{
	$theme = array_map('intval', explode(',', $_POST['idt']) ); 
}

$type_rem = NULL; 

if( isset($_POST['idtr']) && strlen($_POST['idtr']) > 0 )
{
	$type_rem = explode(',', $_POST['idtr']); 
}

$evi = []; 
if( !empty($_POST['evi']) )
{
	$evi = array_map('intval', explode(',', $_POST['evi']) ); 
}

$groupe_lieu = (isset($_POST['gl']) ) ? noui($_POST['gl']) : NULL;
$num_page_rem = (isset($_POST['npr']) ) ? (int)$_POST['npr'] : 0;
$tout = (int)($_POST['tt'] ?? RECUP_TOUT); 

if( $x )
{
	header('content-type: application/json ');
}

$id_date = ($datedu == $dateau ? $datedu : $datedu.'-'.$dateau); 
$id_rem = is_array($type_rem) ? implode('_', $type_rem) : (string)$type_rem; 
$id_theme = is_array($theme) ? implode('_', $theme) : (string)$theme; 

$id = md5(implode('-', [$x, $num_page_rem, $id_rem, $num_page, $tout, $id_date, $lieu, $rayon, $id_theme, $groupe_lieu, implode(',', $evi) ]) );  

$id_cache = cache_id($id);

if(cache($id_cache, TPS_CACHE  ) ) {

$res = []; 

if( $tout == RECUP_TOUT || $tout == RECUP_EVENT )
{
	$realdate = $datepast = '';

	// Liste de proposition 
	$lse = new ls_evenement(array(
		'champ' => EVCH_CAT|EVCH_LIEU|EVCH_DESC|EVCH_NB_DATE|EVCH_CONTACT|EVCH_CAT_GROUPE|EVCH_TOUTE_DATE|EVCH_DATE,
		'fi_date_min' => $datedu,
		'fi_date_max' => $dateau,
		'fi_lieu' => $lieu,
		'fi_grp_lieu' => $groupe_lieu,
		'fi_theme' => $theme,
		'fi_str_actif' => TRUE, 
		'fi_ignore'=>$evi,
		'fi_rayon' => $rayon, 
		'avoir_nb_entre' => TRUE,
		'mode' => reqo::PAGIN, 
	)); 

	$lse->acc_pagin()->mut_num_page($num_page); 
	$lse->acc_pagin()->mut_avec_p(FALSE); 
	$lse->acc_pagin()->mut_url(ADD_SITE_DYN.$datedufr.'/p%pg.html'); 
	$lse->mut_nb_par_page(PL_LIMITE); 
	$lse->requete(); 
}

$param = []; 
$param['datedu'] = strtotime($datedu); 
$param['dateau'] = strtotime($dateau); 

$res['lr_mess'] = '';

if( $x )
{
	if( $tout == RECUP_TOUT || $tout == RECUP_EVENT )
	{
		if( is_array($theme) && empty($theme) )
		{
			$nbe = 0; 
			$tab = ''; 
		}
		else
		{
			$nbe = $lse->acc_nb_entre(); 
			$tab = '';

			while( $e = $lse->parcours() )
			{
				$tab .= $e->planning_html($param); 
			}

			if( $tout == RECUP_TOUT )
			{
				// Calendrier 
				$res['tab_date'] = $lse->nb_par_date(); 

				// Lieux 
				$res['tab_lieu'] = $lse->nb_par_lieu(); 
			}
		}

		$res['pagin_event'] = $lse->acc_pagin()->acc_dyn(); 
		$res['liste_evenement'] = $tab; 
		$res['nbe'] = $nbe; 
	}

	if( $tout == RECUP_TOUT || $tout == RECUP_REM)
	{
		if( !empty($type_rem) && !empty($lieu) )
		{
			$remarquable = new remarquable_ls([
				'fi_type' => $type_rem,
				'fi_lieu' => $lieu,
				'fi_rayon' => $rayon, 
				'avoir_nb_entre' => TRUE, 
				'mode' => reqo::PAGIN,
			]); 

			$remarquable->acc_pagin()->mut_num_page($num_page_rem); 
			$remarquable->requete(); 
			$lsr = []; 

			while($r = $remarquable->parcours() )
			{
				$lsr[] = [
					'html' => $r->html(),
					'lat' => $r->lat(), 
					'long' => $r->long(),
					'titre' => $r->titre(), 
					'id' => $r->id()
				]; 
			}

			$nbr = $remarquable->acc_nb_entre(); 

			if( $tout == RECUP_TOUT )
			{
				$tab_rem = $remarquable->tout_rem(); 
			}
		}
		else
		{
			$lsr = []; 
			$nbr = 0; 
			$tab_rem = []; 
		}

		if( $nbr == 0 )
		{
			if( empty($type_rem) )
			{
				$res['lr_mess'] .= '<div>Choisissez au moins un type de lieu remarquable pour en faire remonter.</div>'; 
			}

			if( empty($lieu) )
			{
				$res['lr_mess'] .= '<div>Saisissez une commune avec ou sans rayon pour faire remonter les lieux remarquables.</div>';
			}

			if( !empty($type_rem) && !empty($lieu) )
			{
				$res['lr_mess'] .= "<div>Aucun lieu remarquable n'a été trouvé dans la zone sélectionné.</div>"; 
			}
		}

		$res['liste_rem'] = $lsr; 
		$res['nbr'] = $nbr; 
		$res['tab_rem'] = $tab_rem; 
	}

	$res['debug']['err'] = $TAB_LOG; 
	echo json_encode($res); 
}
else
{
	$PAT->def_haut(); 
	$PAT->ajt_haut(C_PATRON.'haut-dyn.php'); 

	$PAT->def_patron(); 
	$PAT->ajt_patron('planning_p.php', C_PATRON); 

	$PAT->def_script(); 
	$PAT->ajt_script('fonc.js', ADD_SITE_DYN.'../javascript/');
	$PAT->ajt_script('http://maps.googleapis.com/maps/api/js?key=AIzaSyA61FSozPlNKXIZA4I8zx7BhDwWYFaG-1o', ''); 
	$PAT->ajt_script('jquery-3.1.1.min.js', ADD_SITE_DYN.'../javascript/');
	$PAT->ajt_script('jquery-ui.min.js', ADD_SITE_DYN.'../javascript/jquery-ui-1.12.1/');
	$PAT->ajt_script('datepicker-fr.js', ADD_SITE_DYN.'../javascript/jquery-ui-1.12.1/i18n/');

	$PAT->def_style(); 
	$PAT->ajt_style('mode_dev.css', ADD_SITE_DYN.'../style/'); 
	$PAT->ajt_style('planning.css', ADD_SITE_DYN.'../style/'); 
	$PAT->ajt_style('jquery-ui.min.css', ADD_SITE_DYN.'../javascript/jquery-ui-1.12.1/'); 

	$donne = req('
		SELECT cg.id, CAT_NAME_FR nom, CAT_IMG img, cg.id groupe
		FROM categories_grp cg
		JOIN Categories c
			ON c.groupe = cg.id 
	'); 

	$tab_theme = []; 

	while($do = fetch($donne) )
	{
		$tab_theme[] = new categorie($do); 
		$idt[] = $do['id']; 
	}

	$donne = req('
		SELECT COUNT( DISTINCT(e.id) ) nb, Evenement_date date
		FROM Evenement e
		JOIN Evenement_dates ed
			ON e.id = ed.Evenement_id
		JOIN Categories c 
			ON c.CAT_ID = e.Cat_id 
		WHERE Evenement_date BETWEEN "'.date('Y-m-d').'" AND "'.date('Y-m-d', mktime(0,0,0,date('n')+4) ).'"
		AND Actif=1
		AND c.groupe IN('.implode(',', $idt).') 
		GROUP BY Evenement_date
	');

	$tab_date = []; 

	while($do = fetch($donne) )
	{
		$tab_date[ $do['date'] ] = $do['nb'];
	}

	$title = ''; 

	if( $datedu == $dateau )
	{
		$title .= ' :: '.date_format_title($datedu); 
	}
	else
	{
		$title .= ' :: du '.$datedufr.' au '.$dateaufr; 
	}

	$title .= ( ($num_page+1)!= 1 ) ? ' page '.($num_page+1) : ''; 

	$meta = ''; 

	if( !empty($theme) || is_null($theme) )
	{
		if( !empty($theme) )
		{
			$where = 'WHERE CAT_ID IN ('.implode(',', $theme).') '; 
		}
		else
		{
			$where = ''; 
		}

		$donne = req('SELECT CAT_NAME_FR nom FROM Categories '.$where ); 

		while($do = fetch($donne) )
		{
			$enom = explode(',', $do['nom']); 
			$meta .= $enom[0].', '; 
		}
	}

	$res['lr_mess'] = 'Saisissez une commune avec ou sans rayon pour faire remonter les lieux remarquables.'; 

	$tabm = tab_mois($tab_date); 
	$ch_logo = ADD_SITE_DYN.'../img/bouton/'; 
	$tab_lieu = $lse->nb_par_lieu(); 

	$banderol = visuel_hasard('Banderol'); 

	req('UPDATE Bandeaux SET Affichages=Affichages+1 WHERE id='.$banderol['id']); 

	require PATRON; 
}

// Fin cache 
}
cache(); 
