<?php
require_once '../../include/init.php'; 
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'structure_class.php'; 
require_once C_INC.'structure_fonc.php'; 

http_param(array('u' => 0, 'rech' => '', 'rech_u' => '', 'p' => 0, 'idu'=>array(), 'me'=>array(), 'mi' =>array(), 'md' =>array() ) );

// Traitement 
if(!empty($idu) )
{
	foreach($idu as $i => $idum )
	{
		$droit = (isset($me[$i])?STR_EVENEMENT:0)|(isset($mi[$i])?STR_MODIFIER:0)|(isset($md[$i])?STR_DROIT:0); 
		str_mod_droit($idum, $u, $droit); 
	}
}

$donne = new reqa('
	SELECT absint::sd.droit, absint::s.id, secuhtml::s.nom login
	FROM structure_droit sd
	LEFT JOIN structure s
		ON s.id = sd.structure
	WHERE sd.utilisateur='.$u.'
	ORDER BY s.nom
'); 

if( !empty($rech_u) )
{
	$like = 'LIKE(\'%'.secubdd($rech_u).'%\')'; 
	$rech_u = new reqa("SELECT secuhtml::nom login, absint::id FROM structure 
		WHERE ( nom $like)
		AND id NOT IN (
			SELECT structure FROM structure_droit WHERE utilisateur=".absint($u)."
		)
	"); 
}

$url_rech = urlencode($rech); 

require PATRON;
