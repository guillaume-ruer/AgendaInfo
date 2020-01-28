<?php
include '../../include/init.php'; 
include C_INC.'reqa_class.php'; 
include C_INC.'symbole_class.php'; 

if(isset($_GET['id']) && droit(GERER_SYMBOLE) )
{
	symbole::sup($_GET['id']); 	
	mess('Symbole supprimé.');
}

$donne = new reqo([
	'sorti' => 'symbole',
	'mode' => reqo::NORMAL
]);

$donne->requete('
	SELECT CAT_ID id, CAT_NAME_FR nom, CAT_IMG img, cg.nom_fr groupe, width, height
	FROM Categories c
	LEFT JOIN categories_grp cg 
		ON c.groupe = cg.id
	ORDER BY groupe, CAT_NAME_FR 
'); 

include PATRON;
