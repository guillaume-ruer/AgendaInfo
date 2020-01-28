<?php
require_once '../../include/init.php'; 
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'structure_class.php'; 
require_once C_INC.'structure_fonc.php'; 

$PAT->ajt_haut('str-menu_p.php', C_ADMIN.'structure/patron/');
http_param(array('ids' => 0, 'rech' => '', 'p' => 0, 'idu'=>array(), 'me'=>array(), 'mi' =>array(), 'md' =>array() ) );

if(!str_droit_utilisateur($ids, STR_DROIT) )
{
	page_erreur(); 
}

// Traitement 
if(!empty($idu) )
{
	foreach($idu as $i => $idum )
	{
		$droit = (isset($me[$i])?STR_EVENEMENT:0)|(isset($mi[$i])?STR_MODIFIER:0)|(isset($md[$i])?STR_DROIT:0); 
		str_mod_droit($ids, $idum, $droit); 
	}
}

if( !($str = str_init( $ids ) ) )
{
	page_erreur(); 
}

$donne = new reqa('
	SELECT absint::sd.droit, absint::u.id, secuhtml::User login
	FROM structure_droit sd
	LEFT JOIN Utilisateurs u
		ON u.id = sd.utilisateur
	WHERE sd.structure='.$ids.'
	ORDER BY u.User
'); 

if( !empty($rech) )
{
	$rech = new reqa('SELECT secuhtml::User login, absint::id FROM Utilisateurs 
		WHERE (prenom LIKE(\'%'.secubdd($rech).'%\')
		OR User LIKE(\'%'.secubdd($rech).'%\')
		OR nom LIKE(\'%'.secubdd($rech).'%\') )
		AND id NOT IN (
			SELECT utilisateur FROM structure_droit WHERE structure='.absint($ids).'
		)
	'); 
}

require PATRON;
