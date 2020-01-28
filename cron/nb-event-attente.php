<?php
include '../include/init.php'; 
include C_INC.'mail_fonc.php'; 
include C_INC.'fonc_memor.php'; 
include C_INC.'evenement_class.php'; 

/*
$date = rappel('mail-nb-event-attente');
echo date('d/m/Y H:i', $date).'<br />'; 
*/ 

// lock( 'mail-nb-event-attente', MODE_DEV ? 1 : 23*3600 ); 

$jr = date('w'); 

if( !in_array( $jr, array(0, 6) ) ) 
{
	$ddeb = date('Y-m-d'); 
	$dfin = date('Y-m-d', time() + 120*24*3600 ); 
	// Nombre en attente hors LEI
	$nbe_attente = nb_entre('
		SELECT COUNT(e.id) nbe_attente
		FROM Evenement e
		LEFT JOIN Evenement_dates ed
			ON e.id = ed.Evenement_id 
		WHERE Evenement_Date BETWEEN \''.$ddeb.'\' AND \''.$dfin.'\'
		AND e.lei = 0 
		AND Actif = 0 
		GROUP BY e.id 
	'); 

	// Liste des structure ayant saisi la veille 
	$ls_str = req('
		SELECT COUNT(DISTINCT e.id) nb_event, s.nom FROM `Evenement` e
		JOIN structure_contact sc 
			ON Contact_id = sc.id 
		JOIN structure s
			ON s.id = sc.id_structure
		WHERE DATE( e.Creat_datetime ) = DATE( DATE_SUB( NOW(), INTERVAL 1 DAY ) ) 
		AND lei = 0
		GROUP BY s.id 
	'); 

	$str = ''; 
	while( $do = fetch($ls_str) )
	{
		$str = $do['nom'].' ('.$do['nb_event'].")\n";	
	}

	// Nombre d'affichette crée la veille 
	$donne_nb_aff= req('
		SELECT COUNT(DISTINCT e.id) nb_event
		FROM Evenement e
		WHERE DATE( e.Creat_datetime ) = DATE( DATE_SUB( NOW(), INTERVAL 1 DAY ) ) 
		AND type='.evenement::AFFICHE.' 
	'); 

	$nb_aff = fetch($donne_nb_aff); 
	$nb_aff = $nb_aff['nb_event'];

	// Nombre d'événement du CRT entré la veille 
	$donne_crt = req('
		SELECT COUNT(DISTINCT e.id ) nb_event
		FROM Evenement e
		WHERE DATE( e.Creat_datetime ) = DATE( DATE_SUB( NOW(), INTERVAL 1 DAY ) ) 
		AND lei != 0
	');
	$donne_crt = fetch($donne_crt); 
	$nb_crt_hier = $donne_crt['nb_event'];

	// Nombre d'événement du CRT entré la veille et mis en actif automatiquement 
	$today = mktime(0,0,0,date('n'), date('j'), date('Y') ); 
	$donne_crt = req('
		SELECT SUM(auto_actif) AS nb_aa, SUM(nbsup) AS nb_sup, SUM(nbins) AS nb_ins 
		FROM stat_lei
		WHERE time BETWEEN '.($today-3600*24).' AND '.$today.'
	');
	$donne_crt = fetch($donne_crt); 
	$nb_crt_aa_hier = $donne_crt['nb_aa'];
	$nb_crt_sup_hier = $donne_crt['nb_sup'];
	$nb_crt_ins_hier = $donne_crt['nb_ins'];

	// Nombre totale d'événement du CRT masqué à venir sur l'année en cours  
	$donne_crt = req('
		SELECT COUNT(*) nb_event 
		FROM ( SELECT COUNT(*) 
			FROM Evenement e
			LEFT JOIN Evenement_dates ed
			ON ed.Evenement_id = e.id 
			WHERE ed.Evenement_Date >= DATE( NOW() ) 
			AND lei != 0
			AND Actif = '.evenement::MASQUE.' 
			GROUP BY e.id 
		) tmp
	');

	$donne_crt = fetch($donne_crt); 
	$crt_tot_masque_futur = $donne_crt['nb_event'];

	// Nombre totale d'événement du CRT masqué passé sur l'année en cours. 
	$donne_crt = req('
		SELECT COUNT(*) nb_event 
		FROM ( SELECT COUNT(*) 
			FROM Evenement e
			LEFT JOIN Evenement_dates ed
			ON ed.Evenement_id = e.id 
			WHERE ed.Evenement_Date BETWEEN \''.date('Y').'-01-01\' AND DATE( NOW() )  
			AND lei != 0
			AND Actif = '.evenement::MASQUE.' 
			GROUP BY e.id 
			HAVING MAX(ed.Evenement_Date) < DATE(NOW() )
		) tmp
	');

	$donne_crt = fetch($donne_crt); 
	$crt_tot_masque_passe = $donne_crt['nb_event'];
	
	// Nombre total d'événement activé automatiquement sur l'année en cours 
	$donne_crt = req('
		SELECT SUM(auto_actif) AS nb_aa, SUM(nbsup) AS nb_sup
		FROM stat_lei
		WHERE time BETWEEN '.mktime(0,0,0,0,0,date('Y') ).' AND '.$today.'
	');
	$donne_crt = fetch($donne_crt); 
	$nb_crt_aa_annee = $donne_crt['nb_aa'];
	$nb_crt_sup_annee = $donne_crt['nb_sup'];

	// Nombre total d'événement sur l'année en cours du CRT activé par un modérateur 
	$donne = req('
		SELECT COUNT(DISTINCT(e.id) ) nb_event
		FROM Evenement e
		JOIN historique h
			ON h.idevent = e.id 
		WHERE 
		DATE(e.Creat_datetime) BETWEEN \''.date('Y').'-01-01\' AND \''.date('Y').'-12-31\' 
		AND e.lei != 0
		AND h.idutr != 414
		AND h.etat = 1 
	'); 

	$do = fetch($donne);
	$nb_crt_actif_mod = $do['nb_event'];

	// Nombre d'événement total sur l'année en cours 
	$donne = req('
		SELECT COUNT(DISTINCT(e.id) ) nb
		FROM Evenement e 
		WHERE e.lei != 0
		AND DATE(e.Creat_datetime) BETWEEN \''.date('Y').'-01-01\' AND \''.date('Y').'-12-31\' 
	'); 

	$do = fetch($donne);
	$nb_crt_tot_anne = $do['nb'];

	// 5 structure ayant le plus saisi 
	$donne = req('
		SELECT COUNT( * ) nbe, s.nom
		FROM Evenement e
		JOIN structure_contact sc ON sc.id = e.Contact_id
		JOIN structure s ON s.id = sc.id_structure
		WHERE DATE( Creat_datetime )
		BETWEEN \''.date('Y').'-01-01\'
		AND \''.date('Y').'-12-31\'
		AND lei =0
		GROUP BY s.id
		ORDER BY COUNT( * ) DESC
		LIMIT 5
	'); 

	$str_plus = ''; 

	while( $do = fetch($donne) )
	{
		$str_plus .= $do['nom'].' ('.$do['nbe'].")\n"; 
	}

	// Structure inscrite la veille (le we si lundi). 

	$fin = mktime(23,59,0, date('n'), date('j') ); 
	$deb = $fin - 24*3600*($jr==1 ? 3 : 1); 
	$donne = req('SELECT * FROM structure WHERE date_adhesion BETWEEN '.$deb.' AND '.$fin.' ');
	$adh =''; 
	while($str = fetch($donne) )
	{
		$adh .= $str['nom'].' (le '.date('d/m/Y à H:i', $str['date_adhesion']).")\n"; 
	}

	if( empty($adh) )
	{
		$adh = 'Aucune'; 
	}

	// Envoie du mail 
	// , 
	$mail = 'adhesion@info-limousin.com'; 

	$donne = req('SELECT email FROM Utilisateurs WHERE compte_rendu '); 
	while( $do = fetch($donne) )
	{
		if( preg_match('`^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$`i', $do['email']) )
			$mail .= ','.$do['email'];
	}

	$sujet = $nbe_attente.' événement(s) en attente'; 

	$str = empty($str) ? "Aucune\n" : $str ;

	$nb_ajt_masque = $nb_crt_ins_hier-$nb_crt_aa_hier; 


	$message = <<<START
Bonjour, 

Il y a $nbe_attente événement(s) en attente. 

Adhésions récentes : 
$adh

Structure ayant saisi hier : 
$str
Structure ayant le plus saisi au cours de l'année : 
$str_plus
Nombre d'affichettes saisi la veille : $nb_aff.

CRT Hier
Nombre d'évenement ajouté : $nb_crt_hier.
Nombre d'évenement ajouté et mis en actif automatiquement : $nb_crt_aa_hier.
Nombre d'évenement ajouté et mis en masqué : $nb_ajt_masque.
Nombre d'évenement supprimé : $nb_crt_sup_hier.

CRT Masqué 
Nombre d'évenement masqué à venir : $crt_tot_masque_futur.
Nombre d'évenement masqué passé : $crt_tot_masque_passe.

CRT sur l'année 
Nombre total d'evenement : $nb_crt_tot_anne.
Nombre d'évenement mis en actif automatiquement : $nb_crt_aa_annee.
Nombre d'évenement supprimé : $nb_crt_sup_annee.
Nombre d'évenement mis en actif par un modérateur : $nb_crt_actif_mod.

Bonne journée,
Info-limousin.com 
START;

	mel($mail, $sujet, $message, FALSE); 
	
}

imp( $mail, $sujet, $message ); 
