<?php
require C_INC.'planning_fonc.php'; 

if( !droit(ADMIN) )
{
	senva(); 
}

$PAT->def_patron(); 
$PAT->ajt_patron('planning_p.php', C_PATRON); 
$PAT->ajt_script('http://maps.googleapis.com/maps/api/js?sensor=false', ''); 


$PAT->ajt_script('jquery.js');
$PAT->ajt_script('jquery-ui-1.11.4.custom/jquery-ui.min.js');

$PAT->def_style(); 
$PAT->ajt_style('mode_dev.css'); 
$PAT->ajt_style('planning.css'); 
$PAT->ajt_style('javascript/jquery-ui-1.11.4.custom/jquery-ui.min.css', RETOUR); 

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

$tabm = tab_mois($tab_date); 

require PATRON; 
