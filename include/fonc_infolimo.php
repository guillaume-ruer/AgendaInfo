<?php
define('TP_NORMAL', 0 );
define('TP_EXTERNE', 1 ); 
define('TP_RSS', 2 ); 
define('TP_RSS_EXT', 3 ); 
define('TP_VILLE', 4 ); 
define('TP_ADHERENT', 5 ); 

function secuhtml_tab(&$donne)
{
	foreach($donne as $id => $do )
	{
		$donne[$id] = secuhtml($do);  
	}
}

/*
	Sort les donnée de l'adhérent 	
*/

function nom_adherent($id_adherent)
{
	$donne = req('
		SELECT c.adherent AS nom_adherent, c.logo, c.adresse, c.site, l.Lieu_Ville AS ville, Telephone AS telephone, l.Lieu_CP as cp
		FROM Contact AS c  
		LEFT JOIN Lieu AS l
			ON l.Lieu_ID = c.Lieu
				WHERE c.id = '.(int)$id_adherent.' 
		LIMIT 1 ');
	$do = fetch($donne); 
	secuhtml_tab($do);
	return $do; 
}

/*
	css_externe 
	paramêtre : le code de l'appelle externe 
	retour : nom du css externe 
*/

function nom_template( $code )
{
	$donne = req('SELECT template FROM Externe WHERE code='.$code.' LIMIT 1 '); 
	$do = fetch($donne); 
	return secuhtml($do['template']); 
}

/*
	param : numéro de langue 
	retourne le code de langue 
*/

function langue($num )
{
	$tab_langue = array( 1 =>  "fr_FR.utf8", 2=>"en_EN.utf8" ); 
	$langue = (isset($tab_langue[ $num ]) ) ? $tab_langue[ $num ] : $tab_langue[ 1 ] ; 
	return $langue; 
}

/*
	param : une variable passé par référence 
	retour : rien
	post-condition : la variable vaut '' si elle est vide, sinon elle est casté en entier. 
*/
function nombre_vide(&$nb)
{
	$nb = (empty($nb) ) ? '' : (int)$nb; 
}

/*
	retour true si la variable contient une date au format YYYY-MM-JJ, false sinon 
*/

function verif_date($date)
{
	return (preg_match('`[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}`', $date ) );
}


function url_normal($date, $lieu, $groupe_lieu, $theme, $pg=NULL, $langue= ID_LANGUE, $joli='Tout le limousin, tous les thèmes' )
{
	nombre_vide($theme);
	nombre_vide($lieu); 
	nombre_vide($groupe_lieu);
	$date = ( verif_date($date) ) ? $date : '' ; 
	$pg = (is_null($pg) ) ? '%pg' : (int)$pg;  
	$joli = str_replace('_', ' ', $joli);
	return joli_url($joli).'+'.$pg.'_'.$date.'_'.$lieu.'_'.$groupe_lieu.'_'.$theme.'_'.$langue.'_0.html'; 
}

function url_externe($date, $code, $theme, $pg = NULL, $langue = ID_LANGUE )
{
	nombre_vide($code );
	nombre_vide($theme); 
	$date = ( verif_date($date) ) ? $date : '' ; 
	$pg = (is_null($pg) ) ? '%pg' : (int)$pg;  
	return $pg.'_'.$date.'_'.$code.'_'.$theme.'_'.$langue.'_1.html'; 
}

function url_ville($lieu, $pg = NULL, $langue = ID_LANGUE, $joli = 'Ville' )
{
	$pg = (is_null($pg) ) ? '%pg' : (int)$pg;  
	return ADD_SITE.joli_url($joli).'/'.$pg.'_'.(int)$lieu.'_'.$langue.'_2.html'; 
}

function url_adherent($id, $pg = NULL, $langue = ID_LANGUE, $joli='Adhérent' )
{
	$pg = (is_null($pg) ) ? '%pg' : (int)$pg;  
	return ADD_SITE.joli_url($joli).'/'.$pg.'_'.(int)$id.'_'.(int)$langue.'_3.html';
}

function joli_url($chaine)
{
	$chaine = str_replace(', ', ',', trim($chaine) );
	$chaine = preg_replace('` +`', '-', $chaine );
	$chaine = preg_replace('`-+`', '-', $chaine );
	return $chaine;
}
/*
	Param : L'identifiant du lieu
	Retour : le nom du lieu en fonction de son identifiant.

*/
function nom_lieu($id_lieu)
{
	$donne = req('
		SELECT Lieu_Ville as nom_lieu , Lieu_Dep as num_dep, google_map, mairie, office, internet, facebook, wikipedia, logo, image1, lg.Nom AS departement
		FROM Lieu AS l
		LEFT JOIN Lieu_join AS lj
			ON lj.id_lieu = l.Lieu_ID
		LEFT JOIN Lieu_grp AS lg
			ON lg.id = lj.id_groupe
		WHERE l.Lieu_ID='. (int)$id_lieu . ' 
		LIMIT 1 
	');
	
	$do = fetch($donne); 

	secuhtml_tab($do); 
	return $do; 
}

/*
	Idem avec les groupe de lieu 
*/

function nom_groupe_lieu($id_lieu ) 
{
	$donne = req('SELECT Nom AS nom FROM Lieu_grp WHERE id='.(int)$id_lieu . ' LIMIT 1 ');
	$do = fetch($donne); 
	return secuhtml($do['nom']);
}

/*
	Param : l'identifiant du theme 
	Retour : le nom du thème 
*/
function nom_theme($id_theme)
{
	$donne = req('SELECT nom_fr, nom_en  FROM categories_grp WHERE id = '. (int)$id_theme .' LIMIT 1 '); 
	$do = fetch($donne);
	$nom = (ID_LANGUE == 2 AND !empty($do['nom_en']) ) ? $do['nom_en'] : $do['nom_fr'] ; 
	return secuhtml($nom); 

}

/*
	Donne les infos du 
*/

function info_filtre($realdate, $idlieu, $idgroupelieu, $idtheme )
{
	//La petite phrase indiquant les élément du filtrage
	$datedeb = date_format_fr($realdate); 

	if(!empty($idlieu) )
	{
		$do_lieu = nom_lieu($idlieu); 
		$lieu_filtre = $do_lieu['nom_lieu'];
	}
	elseif(!empty($idgroupelieu) )
	{
		$lieu_filtre = nom_groupe_lieu($idgroupelieu); 
	}
	else
	{
		$lieu_filtre = ' Tout le limousin '; 
	}

	$nom_theme = (!empty($idtheme ) ) ? nom_theme($idtheme) : ' tous les thèmes ';


	return $lieu_filtre . ' - à partir du ' . $datedeb . ' - ' . $nom_theme ;  
}


/*
	param : 
		Le nombre d'élément par page
		Le nombre d'élément totale
		le type de la page
		le tableau des parametre (en fonction du type de page )
	retour : un tableau contenant la liste des lien à affiché, sous la forme 
		array(
			array( class, lien, nb ),
			array( class, lien, nb )
		)
	
*/
function pg_event($nbparpage, $nb_entre, $num_page, $lien )
{
	$pagination = array(); 
	$i = 0;
	while($i < $nb_entre /$nbparpage)
	{
		$class = ($num_page == $i ) ? 'actif' : 'inactif' ; 
		
		$url = str_replace('%pg', $i, $lien); 

		$pagination[] = array(
			'lien' => $url, 
			'nb' => $i+1,
			'class' => $class
		);
		$i ++; 
	}      

	return $pagination; 
}

/*
	param : chaine de caractère 
	retour : une chaine de caractère, les lien on été remplacé (commenceant par http ou www ) 
*/

function lien_html($chaine)
{
	if(strpos($chaine, 'http' ) )
	{
		$chaine = preg_replace('`(http://([/a-zA-Z0-9_.-]+)) ?`', '[<a href="$1" >$2</a>]', $chaine); 
	}
	else
	{
		$chaine = preg_replace('`(www\.[/a-zA-Z0-9_.-]+) ?`', '[<a href="http://$1" >$1</a>]', $chaine); 
	}

	return $chaine;
}

/*
	param : la date envoyer par l'utilisateur, sous la form Y-m-d,
		deux variable passé par référence qui contiendrons respectivement la date de début et la date de fin 
	
	retour : les variable passé par référence sont modifié 
	
*/
function mes_date($input_date, $nb_jour_visible, &$realdate, &$datepast)
{
	//init
	$now = date('Y-m-d'); 

	if(!empty($input_date) AND substr_count($input_date, '-' ) == 2 )
	{
		$tmpdate = explode('-',$input_date );
		
		//Vérification, est ce que la date envoyer est plus ancienne que la date actuelle 
		if( mktime(0,0,0,$tmpdate['1'],$tmpdate['2'],(int)$tmpdate['0'] ) < mktime(0, 0, 0, date("m") , date("d"), date("Y")) )
		{
			$input_date = $now;
		}
	}
	else
	{
		$input_date = $now; 	
	}
	
	$realdate = $input_date;

	$datepast = explode('-',$realdate);
	$datepast = date('Y-m-d', mktime(0, 0, 0, $datepast['1'] , $datepast['2']+$nb_jour_visible, $datepast['0']));
	
}

function date_fr2en($date)
{
	if( est_date_fr($date) )
	{
		list($d,$m,$Y) = explode('-', $date); 
		return "$Y-$m-$d"; 
	}

	return FALSE; 
}

function date_en2fr($date)
{
	if( est_date($date) )
	{
		list($Y,$m,$d) = explode('-', $date); 
		return "$d-$m-$Y"; 
	}

	return FALSE; 
}

/*
	p : date au format d/m/Y
	r : date au format Y-m-d
*/
function date_format_traitement($date )
{
	$realdate = explode('/',$date);
	$realdate = array_reverse($realdate);
	$realdate = implode('-',$realdate);
	return $realdate; 
}
/*
	p : date au format Y-m-d
	r : date au format d/m/Y
*/

function date_format_fr_slashes($date)
{
	$date = explode('-',$date);
	$date = array_reverse($date);
	$date = implode('/',$date);
	return $date; 
}

/*
	Donné une date au format Y-m-d
	retourn une date au format jour num_jour etc
*/

function date_format_fr($date)
{
	return time_format_fr(strtotime($date) ); 
}

function time_format_fr($time)
{
	$tmp = explode('/', strftime("%w/%d/%m/%Y", $time ) ); 
	$date = jr_num2str($tmp[0]); 
	$date .= ' '.$tmp[1]; 
	$date .= ' '.moi_num2str( (int)$tmp[2] );
	$date .= ' '.$tmp[3]; 
	return $date; 
}

function date_format_title($date)
{
	return time_format_title(strtotime($date) ); 
}

function time_format_title($time)
{
	$tmp = explode('/', strftime("%w/%d/%m/%Y", $time ) ); 
	$date = jr_num2str($tmp[0]); 
	$date .= ' '.$tmp[1]; 
	$date .= ' '.strtolower(moi_num2str( (int)$tmp[2] ) );
	$date .= ' '.$tmp[3]; 
	return $date; 
}



function aujourdhui()
{
	return time_format_fr(time() ); 
}

function jr_num2str($num)
{
	static $nom_jour = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']; 
	return $nom_jour[$num]; 
}

function moi_num2str($num)
{
	static $nom_mois = [1=>'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
		'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
	return $nom_mois[$num]; 
}

/*
	param : l'id du theme actuelle 
	retour : le tableau des theme, contenant des éléments sous la forme
		array( id, nom, s ); 
	
		s vaut selected="selected" ou "" en fonction du parametre
*/
function opt_theme( $id_theme = 0 )
{
	$tab_theme = array(); 
	$tab_theme[] = array( 'id' => '' , 'nom' => 'Choisir un thème' ,'s' => '' ); 
	$donne = req('SELECT * FROM categories_grp ');

	while($do = fetch($donne) )
	{
		$s = ( $id_theme == $do['id'] ) ? ' selected="selected" ' : ''; 
		$nom = (ID_LANGUE == 2 AND !empty($do['nom_en']) ) ? $do['nom_en'] : $do['nom_fr'] ; 
		$tab_theme[] = array( 
			'id' => (int)$do['id'] , 	
			'nom' => secuhtml( $nom ) ,
			's' => $s
		); 
	}

	return $tab_theme; 
}

function opt_theme_non_vide($realdate, $datepast ) 
{
	$tab_theme = array(); 
	$tab_theme[] = array( 'id' => '' , 'nom' => 'Choisir un thème' ,'s' => '' ); 
	$donne = req("
		SELECT DISTINCT CG.nom_fr nom, CG.id 
                FROM Evenement_dates AS ED
                LEFT JOIN Evenement AS E 
                        ON E.id = ED.Evenement_id
		LEFT JOIN Categories AS C 
			ON C.CAT_ID = E.Cat_id
		LEFT JOIN categories_grp AS CG
			ON CG.id = C.groupe
                WHERE E.Actif = 1 
                AND ED.Evenement_Date >= '$realdate'
                AND ED.Evenement_Date < '$datepast'
                ORDER BY CG.nom_fr
	"); 

	while($do = fetch($donne) )
	{
		$tab_theme[] = array( 
			'id' => (int)$do['id'] , 	
			'nom' => secuhtml( $do['nom'] ),
		); 
	}

	return $tab_theme; 


     
}

/*
	param : date de début, date de fin, au format Y-m-d
	retour : le tableau des lieu spécial

*/

function opt_lieu_spe($realdate, $datepast ) 
{
	$lieu_qry = ' 
	SELECT DISTINCT L.Lieu_Ville AS nm, L.Lieu_Dep, L.Lieu_ID AS id, COUNT(DISTINCT E.id ) AS nb_event
	FROM Evenement_dates AS ED
	LEFT JOIN Evenement_lieux AS EL 
		ON EL.Evenement_id = ED.Evenement_id
	LEFT JOIN Lieu AS L 
		ON L.Lieu_ID = EL.lieu_id
	LEFT JOIN Evenement AS E
		ON E.id = ED.Evenement_id
	WHERE E.Actif=1 
	AND ED.Evenement_Date >=\''.$realdate.'\'
	AND ED.Evenement_Date <\''.$datepast.'\'
	GROUP BY L.Lieu_Ville
	ORDER BY nb_event DESC 
	LIMIT 10 
	'; 

	$tab_lieu = array(); 	
	$tab_lieu[] = array('id' => '' , 'nom' => 'Tous les lieux' ); 
	$lieu_result =req($lieu_qry);
	
	while($do = fetch($lieu_result) )
	{
		$tab_lieu[] = array(
			'id' => (int)$do['id'] ,
			'nom' => secuhtml($do['nm']) 
		);
	}

	return $tab_lieu; 
}

/*
	p : date de début, date de fin, id du lieu actuelle 
	r : le tableau des lieu, contenant au moins un info 
*/

function opt_lieu($realdate, $datepast, $lieu = 0, $format='option' )
{
	//Init 
	$tab_lieu = array(); 
	// les lieux
	$lieu_qry = "
		SELECT DISTINCT L.Lieu_Ville, L.Lieu_Dep, L.Lieu_ID, L.lat, L.lng
		FROM Evenement_dates AS ED
		LEFT JOIN Evenement_lieux AS EL 
			ON EL.Evenement_id = ED.Evenement_id
		LEFT JOIN Lieu AS L 
			ON L.Lieu_ID = EL.lieu_id
		LEFT JOIN Evenement AS E 
			ON E.id = ED.Evenement_id
		WHERE E.Actif = 1 
		AND ED.Evenement_Date >= '$realdate'
		AND ED.Evenement_Date < '$datepast'
		AND L.Lieu_Ville != '' 
		ORDER BY L.Lieu_Ville
	";

	$lieu_result = req( $lieu_qry );

	if( $format == 'option' )
	{
		$tab_lieu[] = array('value' => '' , 'nom' => 'Tous les lieux' , 's' => '', 'lat' => '', 'long' => ''); 

		while($lieu_row = fetch($lieu_result))
		{
			$s = ($lieu_row['Lieu_ID'] == $lieu ) ? 'selected="selected"' : ''; 

			$tab_lieu[] = array(
				'value' => (int)$lieu_row['Lieu_ID'],
				'nom' =>  secuhtml($lieu_row['Lieu_Ville'])." (". (int)$lieu_row['Lieu_Dep']. ")" ,  
				'lat' => str_replace(',', '.', (float)$lieu_row['lat']),
				'long' => str_replace(',', '.', (float)$lieu_row['lng']), 
				's' => $s  
			);
		}
	}
	elseif($format=='json')
	{
		while($lieu_row = fetch($lieu_result))
		{
			$tab_lieu[ (int)$lieu_row['Lieu_ID'] ] = array(
				'id' => (int)$lieu_row['Lieu_ID'],
				'value' => $lieu_row['Lieu_Ville']." (". (int)$lieu_row['Lieu_Dep']. ")" ,  
				'lat' => str_replace(',', '.', (float)$lieu_row['lat']),
				'long' => str_replace(',', '.', (float)$lieu_row['lng']), 
			);
		}
	}

	return $tab_lieu;
}

/*
	p : date de début, date de fin, id du lieu actuelle 
	r : le tableau des groupe de lieu, contenant au moins un info 
*/

function opt_groupe_lieu($realdate, $datepast, $lieu = 0, $dep=FALSE )
{
	$tab_lieu = array(); 
	$tab_lieu[] = array('value' => '', 'nom' => 'Tout le Limousin' , 's'=>'' ); 

	$where = ''; 

	if( $dep )
	{
		$where = ' AND Lieu_grp.ordre=1 '; 
	}

	$sql = "
			SELECT DISTINCT Lieu_grp.id, Lieu_grp.Nom  
			FROM Lieu_grp 
			LEFT JOIN Lieu_join
				ON Lieu_join.id_groupe = Lieu_grp.id
			LEFT JOIN Lieu
				ON Lieu.Lieu_id = Lieu_join.id_lieu
			LEFT JOIN Evenement_lieux AS EL
				ON EL.Lieu_id = Lieu.Lieu_id
			LEFT JOIN Evenement_dates AS ED 
				ON ED.Evenement_id = EL.Evenement_id
			LEFT JOIN Evenement AS E 
				ON E.id = ED.Evenement_id 
			WHERE E.Actif = 1 
			AND ED.Evenement_Date >= '$realdate'
			AND ED.Evenement_Date < '$datepast'
			$where	
			ORDER BY Lieu_grp.ordre, Lieu_grp.Nom
		";

	$lieu_result = req($sql);

	// les groupes de lieux
	while($lieu_row = fetch($lieu_result) )
	{
		$s = ($lieu_row['id'] == $lieu ) ? 'selected="selected"' : ''; 

		$tab_lieu[] = array(
			'value' => (int)$lieu_row['id'], 
			'nom' => secuhtml($lieu_row['Nom']),
			's' => $s 
		);
	}

	return $tab_lieu;
}

class opt_groupe_lieu
{
	public $lieu_result = '';
	public $lieu = 0; 

	function __construct($realdate, $datepast, $lieu = 0)
	{
		$sql = "
			SELECT DISTINCT Lieu_grp.id, Lieu_grp.Nom  
			FROM Lieu_grp 
			LEFT JOIN Lieu_join
				ON Lieu_join.id_groupe = Lieu_grp.id
			LEFT JOIN Lieu
				ON Lieu.Lieu_id = Lieu_join.id_lieu
			LEFT JOIN Evenement_lieux AS EL
				ON EL.Lieu_id = Lieu.Lieu_id
			LEFT JOIN Evenement_dates AS ED 
				ON ED.Evenement_id = EL.Evenement_id
			LEFT JOIN Evenement AS E 
				ON E.id = ED.Evenement_id 
			WHERE E.Actif = 1 
			AND ED.Evenement_Date >= '$realdate'
			AND ED.Evenement_Date < '$datepast'
		";

		$this->lieu = (int)$lieu;
		$this->lieu_result = req($sql);
	}

	function f()
	{
		global $value, $nom, $s; 
		
		$do = fetch($this->lieu_result);
		
		$s = ( $this->lieu == $do['id']) ? 'selected="selected"' : '';
		$value = (int)$do['id'];
		$nom = secuhtml($do['Nom']);

		return (bool)$do;
	}
}

function opt_contact($id_adh)
{
	$tab_c = array(
		array('id' => 0, 'nom' => 'Séléctionné un contact', 's' => '' ) 
	); 
	
	$donne = req('
		SELECT TRIM( c.adherent ) AS adh, c.id FROM Utilisateurs AS u
		LEFT JOIN Contact AS c 
			ON c.id = u.contact_id
		WHERE uActif = 1 AND c.adherent != \'\'

		ORDER BY c.type, adh 
	');

	while($do = fetch($donne) )
	{
			}

	return $tab_c;
}


function homogene($texte)
{
	$texte = trim($texte); 

	if( !empty($texte) ) 
	{
		$texte = trim($texte); 
		$texte = ucfirst($texte); 
		$texte = preg_replace('`([0-9]+) *h(eure?s?)?(( *00)|( *([0-9]{2})))?`i', '$1h$6', $texte ); 
		$texte = preg_replace('`([0-9]{2})[. ,-]?([0-9]{2})[. ,-]?([0-9]{2})[. ,-]?([0-9]{2})[. ,-]?([0-9]{2})`', '$1 $2 $3 $4 $5', $texte ); 
		$texte = preg_replace('`([0-9]+) *(kilom.+tres?|km)`i', '$1km', $texte ); 

		if($texte[ strlen($texte)-1 ] != '.' )
		{
			$texte .='.';
		}
	}
	return $texte; 
}
