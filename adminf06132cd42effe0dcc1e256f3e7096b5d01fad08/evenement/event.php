<?php
require '../../include/init.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'categorie_class.php'; 
require_once C_INC.'departement_class.php';
require_once C_INC.'evenement_class.php';
require_once C_INC.'evenement_fonc.php';
require_once C_INC.'ls_evenement_class.php';
require_once C_INC.'ville_class.php'; 
require_once C_INC.'ville_fonc.php'; 
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'adresse_class.php';
require_once C_INC.'structure_class.php';
require_once C_INC.'tarif_class.php';
require_once C_INC.'structure_class.php'; 

define('NB_JOUR_VISIBLE', 120 ); 
define('NB_JOUR_CALENDRIER', 60 );

//Calendrié 
$dos = RETOUR.'jscalendar/';
$PAT->ajt_style('calendar-win2k-cold-1.css', $dos);
$PAT->ajt_style('xform.css');
$PAT->ajt_script('calendar.js', $dos ); 
$PAT->ajt_script('calendar-en.js', $dos.'lang/' ); 
$PAT->ajt_script('calendar-fr.js', $dos.'lang/' ); 
$PAT->ajt_script('calendar-setup.js', $dos ); 

$PAT->ajt_javascript('var C_IMG=\''.RETOUR.'img/xform/\';'); 
$PAT->ajt_javascript('var XSELECT_OPTION=[];'); 
$PAT->ajt_script('xform.js'); 

$PAT->ajt_script('menu_deroulant.js'); 

// Style 
$PAT->ajt_style('event.css'); 

/*
	Donnée du filtrage 
*/


$ch_lieu = new barre_proposition_form(['fichier'=> 'lieu.php', 'class' => 'ville', 'label' => 'Lieu', 'limite' => 1] ); 
$donne = array(
	'lieu' => NULL, 'groupe_lieu' => NULL, 'theme' => NULL, 'etat' => ls_evenement::ACTIF_INACTIF, 
	'lei' => ls_evenement::LEI_SANS, 'id_contact' => NULL, 'struct' => 0, 'grpstr'=>NULL 
);

foreach( $donne as $do => $def )
{
	if( isset($_POST[$do]) )
	{
		${$do} = noui($_POST[$do]); 
	}
	elseif( isset($_GET[$do]) )
	{
		${$do} = noui($_GET[$do]); 
	}
	else
	{
		${$do} = $def; 
	}
}

http_param( array('date' => '', 'pg' => 0, 'rech' => '' ) ); 

if( $tlieu = $ch_lieu->donne() )
{
	$lieu = $tlieu[0]->id(); 
}
elseif( !is_null($lieu) )
{
	$ch_lieu->mut_donne( ville_init($lieu) ); 
}

/*
	Initialisation de la liste 
*/

$date = date_format_traitement($date);

$realdate = $datepast = ''; 
mes_date($date, NB_JOUR_VISIBLE, $realdate, $datepast);

$ls = new ls_evenement(array(
	'champ' => EVCH_CAT|EVCH_DATE|EVCH_LIEU|EVCH_DESC|EVCH_ETAT|EVCH_TARIF|EVCH_CONTACT,
	'fi_date_min' => $realdate, 
	'fi_date_max' => NULL, 
	'fi_lieu' => $lieu,
	'fi_grp_lieu' => $groupe_lieu, 
	'fi_theme' => $theme, 
	'fi_actif' => $etat, 
	'fi_lei' => droit(GERER_LEI) ? $lei : ls_evenement::LEI_TOUT, 
	'fi_str_actif' => FALSE, 
	'fi_recherche' => $rech, 
	'fi_structure' => $struct,
	'fi_grpstr' => $grpstr, 
	'order' => ls_evenement::ORDER_DATE_LIEU
) ); 


if(!droit(GERER_EVENEMENT) )
{
	$ls->mut_fi_structure_droit($MEMBRE->id ); 
}

$date_champ = date_format_fr_slashes($realdate); 
$url_pagin = C_ADMIN.'evenement/event.php?pg=%pg&amp;date='.$date_champ
	.'&amp;lieu='.$lieu.'&amp;groupe_lieu='.$groupe_lieu.'&amp;theme='.$theme
	.'&amp;etat='.$etat.'&amp;lei='.$lei.'&amp;id_contact='.$id_contact
	.'&amp;rech='.urlencode($rech).'&amp;struct='.$struct
	.'&amp;grpstr='.$grpstr; 

$url_actuel = str_replace( '%pg', $pg, $url_pagin); 

$ls->acc_pagin()->mut_max_lien(9999);
$ls->acc_pagin()->mut_url($url_pagin); 
$ls->acc_pagin()->mut_mode(pagin_reqo::COUPE); 
$ls->acc_pagin()->mut_num_page($pg); 

$ls->requete(); 

//Tableau pour les menu déroulant 
$tab_lieu = opt_lieu($realdate, $datepast, $lieu); 
$tab_groupe_lieu = opt_groupe_lieu($realdate, $datepast, $groupe_lieu ); 

$reqtheme = ls_grp_symbole(); 

$reqcat = req('SELECT * FROM Categories');

while($do = fetch($reqcat) )
{
	$tabtheme[] = [
		'id' => $do['CAT_ID'],
		'nom' => $do['CAT_NAME_FR'],
		'img' => $do['CAT_IMG'],
		'width' => $do['width'], 
		'height' => $do['height']
	]; 
}

$tab_etat = array( 
	ls_evenement::ACTIF_INACTIF => 'Actif/Inactif', 
	ls_evenement::TOUT => 'Tout', 
	evenement::ACTIF => 'En diffusion' , 
	evenement::MASQUE=>'Pas en diffusion', 
	evenement::SUPP=>'Supprimé' 
); 
$tab_lei = array( 
	ls_evenement::LEI_TOUT => 'Tout', 
	ls_evenement::LEI_SANS => 'Sans les imports' , 
	ls_evenement::LEI_SEUL =>'Que le lei', 
	ls_evenement::STQ_SEUL => 'Que le Sirtaqui'
); 

//Pour le calendrier js
$jsdate = date('Y,m,d',mktime(0, 0, 0, date('m')-1, date('d')+NB_JOUR_CALENDRIER, date('y'))); 
$jsdate = explode(',' ,$jsdate );
$js_jr = $jsdate[2];
$js_moi = $jsdate[1];
$js_annee = $jsdate[0];

$sel = ' selected="selected" ';

$tab_class_etat = [
	evenement::ACTIF => 'event_actif',
	evenement::MASQUE => 'event_masque',
	evenement::SUPP => 'event_supp', 
];

/*
	Filtre par structure 
*/

if( droit(GERER_EVENEMENT) )
{
	$ls_structure = new reqo( array(
		'mode' => reqo::NORMAL, 
		'sorti' => 'structure'
	) ); 

	$ls_structure->requete('
		SELECT s.id, s.nom 
		FROM Evenement e 
		JOIN Evenement_dates ed
			ON ed.Evenement_id = e.id 
		JOIN structure_contact c
			ON e.Contact_id = c.id 
		JOIN structure s
			ON s.id = c.id_structure 
		WHERE Evenement_Date >= NOW()
		GROUP BY c.id_structure
		ORDER BY s.nom 
	'); 

	$grp_structure = req('SELECT * FROM structure_grp'); 
}

require PATRON; 
