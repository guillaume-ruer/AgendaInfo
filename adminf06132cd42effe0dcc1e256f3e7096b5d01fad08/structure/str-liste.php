<?php
require_once '../../include/init.php'; 
require_once C_INC.'structure_class.php';
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'structure_fonc.php'; 
require_once C_INC.'ls_structure_class.php'; 
require_once C_INC.'ville_class.php'; 

define('STR_NB_PAR_PAGE', 20); 

$PAT->ajt_style('structure.css'); 


http_param(array('p' => 0, 'rech' => '', 'ville' => 0 ) ); 

$str = new ls_structure();
$str->acc_pagin()->mut_num_page($p); 
$str->mut_nb_par_page(STR_NB_PAR_PAGE); 
$str->mut_fi_recherche($rech); 
$str->mut_fi_ville($ville); 
$str->acc_pagin()->mut_url( 'str-liste.php?p=%pg&amp;rech='.$rech.'&amp;ville='.$ville ); 

if(droit(GERER_UTILISATEUR) )
{
	if(isset($_GET['idsd']) )
	{
		str_etat($_GET['idsd'], structure::INACTIF); 	
	}
	elseif(isset($_GET['idsa']) )
	{
		str_etat($_GET['idsa'], structure::ACTIF); 
	}
	elseif(isset($_GET['idssup']) )
	{
		str_sup($_GET['idssup']); 
	}

	$str->mut_fi_droit(FALSE); 
}

$str->requete(); 

$ls_ville = new reqo(array('sorti' => 'ville', 'mode' => reqo::NORMAL ) ); 
$ls_ville->requete('
	SELECT Lieu_ID id, Lieu_Ville nom
	FROM structure s
	JOIN Lieu l
		ON s.ville = l.Lieu_ID
	GROUP BY l.Lieu_ID
	ORDER BY Lieu_Ville 
'); 

include PATRON; 
