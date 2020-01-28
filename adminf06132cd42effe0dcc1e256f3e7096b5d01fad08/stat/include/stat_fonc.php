<?php
function fi_date( $annee, $moi )
{
	$moideb = '01';
	$moifin = '12'; 

	if($moi >= 1 AND $moi <= 12  )
	{
		if( $moi > 0 AND $moi < 10 )
		{
			$moi = '0'.$moi; 
		}
			
		$moideb = (string)$moi; 
		$moifin = (string)$moi;  
	}
	
	return "Evenement_date BETWEEN '$annee-$moideb-01' AND '$annee-$moifin-31'\n"; 
}
/* Lei : */

function stat_lei($annee, $moi )
{
	$stat_lei = new reqa('
		SELECT COUNT(DISTINCT e.id ) AS nb, nometat::Actif AS nom
		FROM Evenement AS e
		LEFT JOIN Evenement_dates AS ed
			ON e.id = ed.Evenement_id
		WHERE lei != 0 
		AND '.fi_date($annee, $moi).'
		GROUP BY Actif 
	', array( 'nb' => 'absint') );

	return $stat_lei;
}

function nometat($num_etat )
{
	$tab_etat = array(
		'Masqué',
		'Actif', 
		'Supprimé'
	);
	
	return isset($tab_etat[ $num_etat ]) ? $tab_etat[ $num_etat ] : $num_etat; 
}

/* Nombre d'événement total de l'année */ 

function nombre_evenement($annee, $moi )
{

	$donne = req('
		SELECT COUNT( DISTINCT Evenement_id ) AS nbe 
		FROM Evenement_dates 
		WHERE '.fi_date($annee, $moi ).' 
	');

	$do = fetch($donne); 
	return (int)$do['nbe']; 
}

/* Nombre d'événement par département */ 

function stat_dep($annee, $moi )
{
	$departement = new reqa('
		SELECT absint::COUNT( DISTINCT ed.Evenement_id ) AS nbe, num2dep::Lieu_Dep AS dep 
		FROM Evenement_dates AS ed 
		LEFT JOIN Evenement_lieux AS el 
			ON ed.Evenement_id = el.Evenement_id 
		LEFT JOIN Lieu 
			ON Lieu.Lieu_ID = el.Lieu_id
		WHERE '.fi_date($annee, $moi ).' 

		GROUP BY Lieu.Lieu_Dep
		ORDER BY dep
	');
	return $departement; 
}

function num2dep($num)
{
	$tab_dep = array(
		0 => 'Sans lieu',
		23 => 'Creuze',
		87 => 'Haute-Vienne', 
		19 => 'Corrèze'
	); 

	$num = absint($num); 

	return $tab_dep[$num].' '.( empty($num) ? '' : '('.$num.')' );
}
/* Nombre d'événement par contact */

function stat_contact( $annee, $moi )
{
	$contact = new reqa('
		SELECT absint::COUNT( DISTINCT e.id ) AS nbe, 
			absint::s.id AS id, secuhtml::s.nom AS adh
		FROM structure AS s
		JOIN structure_contact sc
			ON s.id = sc.id_structure
		LEFT OUTER JOIN Evenement e
			ON e.Contact_id = sc.id 
		LEFT OUTER JOIN Evenement_dates AS ed
			ON e.id = ed.Evenement_id
		WHERE ('.fi_date($annee, $moi ).') 
		GROUP BY s.id 
		ORDER BY adh 
	'); 

	return $contact; 
}
/* Nombre d'événements par categorie */

function stat_categorie($annee, $moi )
{
	$categorie = new reqa('
		SELECT absint::COUNT( DISTINCT ed.Evenement_id ) AS nbe, secuhtml::CAT_NAME_FR AS nom, absint::Categories.CAT_ID AS id
		FROM Evenement_dates AS ed 
		LEFT JOIN Evenement AS e
			ON e.id = ed.Evenement_id
		LEFT JOIN Categories 
			ON Categories.CAT_ID = e.Cat_id
		WHERE '.fi_date($annee, $moi ).' 

		GROUP BY Categories.CAT_ID 
		ORDER BY nom

	');

	return $categorie; 
}

/* Nombre d'événements par theme */

function stat_theme($annee, $moi )
{
	$theme = new reqa('
		SELECT absint::COUNT( DISTINCT ed.Evenement_id ) AS nbe, secuhtml::categories_grp.nom_fr AS nom, absint::categories_grp.id AS id
		FROM Evenement_dates AS ed 
		LEFT JOIN Evenement AS e
			ON e.id = ed.Evenement_id
		LEFT JOIN Categories 
			ON Categories.CAT_ID = e.Cat_id
		LEFT JOIN categories_grp
			ON Categories.groupe = categories_grp.id
		WHERE '.fi_date($annee, $moi ).' 

		GROUP BY categories_grp.id 
		ORDER BY nom
	');

	return $theme;
}

function stat_moderateur($annee, $moi)
{
	if( $moi >= 1 && $moi < 12 )
	{
		$deb = mktime(0,0,0,$moi,1,$annee);
		$fin = mktime(0,0,0,$moi+1,1,$annee);
	}
	elseif( $moi == 12 )
	{
		$deb = mktime(0,0,0,$moi,1,$annee);
		$fin = mktime(0,0,0,1,1,$annee+1);
	}
	else
	{
		$deb = mktime(0,0,0,1,1,$annee); 
		$fin = mktime(0,0,0,1,1,$annee+1); 
	}

	$mod = new reqa('
		SELECT u.User, u.nom, u.prenom, COUNT(h.id) nb
		FROM Utilisateurs u 
		LEFT JOIN historique h
			ON h.idutr = u.id
		WHERE h.etat = 1 AND h.date BETWEEN '.$deb.' AND '.$fin.'
		GROUP BY u.id
	'); 

	return $mod; 
}
