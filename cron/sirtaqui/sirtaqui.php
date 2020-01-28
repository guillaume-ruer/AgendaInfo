<?php
/*
	DELETE FROM Evenement WHERE source=2; 
*/
require '../../include/init.php'; 
require_once C_INC.'evenement_class.php'; 
require_once RETOUR.'cron/evenement_lei_class.php'; 
require_once C_INC.'evenement_fonc.php'; 
require_once C_ADMIN.'lei/include/var-alerte.php'; 

define('PRCENT_TITRE', 30 ); 
define('PRCENT_DESC', 10 ); 
define('TAILLE_TITRE', 70);
define('TAILLE_DESC', 400 ); 
define('ECART_PAS_AUTO', 40 * 24 * 3600 ); 
define('LOCAL', TRUE);
define('LIGNE', FALSE); 

$SOURCE = evenement::STQ;

$pagin = TRUE; 
$import = TRUE; 

$pagin_de = 1;
$pagin_a = 200; 

$nbevent=0; 
$nbajt=0;
$nbmod=0;
$nbact=0; 
$cle_fam = '57675d2d-3bd8-4070-9786-2b08a5142938';

$data = stq_tab_fam($import, $cle_fam);  

foreach($data['value'] as $event )
{

/*
$aff = TRUE; 
$txt = ''; 
$td = explode('#', $event['DATESCOMPLET'] );

foreach($td as $d)
{
	list($deb, $fin, $hdeb1, $hfin1, $hdeb2, $hfin2, $dferm ) = 
		explode('|', $d); 

	/*
	if( !empty($dferm) )
	{
		$aff = TRUE; 
		$txt .= var_dump_str($tf)."\n"; 
	}

}

$gh = stq_conv_date_complet($event['DATESCOMPLET']); 

if( count($gh) > 1 )
{
	$aff = TRUE; 
	$txt .= var_dump_str($gh); 
}

if( $aff )
{
	var_dump($event['DESCRIPTIFOT']); 
	var_dump($event['DATESCOMPLET']); 
	var_dump($event['DATES']); 
	echo $txt; 
	echo "\n"; 
}

continue; 
	*/

$nbevent++; 

if( $pagin)
{
	if( $nbevent < $pagin_de )
	{
		continue; 
	}

	if( $nbevent > $pagin_a )
	{
		break; 
	}
}


$do = stq_conv_event($event); 
echo $do['id']."\n"; 
$modif = FALSE; 
$actif = TRUE; 
vide_tc(); 

$ev = init_lei($do['id']); 

if( !$ev )
{
	$ev = new evenement_lei; 	
	$modif = TRUE; 
}
else
{
	/*
		Détéctions des modifications éventuelles 
	*/

	// Modif description 
	if(hashfct($do['com']) != $ev->acc_h_com() )
	{
		ajt_tc("La description à été modifié.\n"); 
		$modif = TRUE;
	}

	//Modif Lieu 
	if( hashfct($do['lieu']) != $ev->acc_h_lieu() )
	{
		ajt_tc("Le lieu à été modifié (".$do['lieu'].").\n"); 
		$modif = TRUE;
	}

	//Modif titre 
	if(hashfct($do['titre']) != $ev->acc_h_titre() )
	{
		ajt_tc("Le titre à été modifié.\n"); 
		$modif = TRUE;
	}

	//Modif theme 
	if(hashfct($do['categorie']) != $ev->acc_h_theme() ) 
	{
		ajt_tc("Le thème à été modifié.\n"); 
		$modif = TRUE;
	}

	//Modif contact 
	if( hashfct($do['contact']) != $ev->acc_h_contact() )
	{
		ajt_tc("Le contact à été modifié.\n"); 
		$modif = TRUE;
	}

	// Modif de date 
	$ev->mut_date_lei( select_duau( $ev->acc_id() ) );

	if( hash_duau($do['date']) != hash_duau( $ev->acc_date_lei() ) )
	{
		$mess = "Dates non correspondantes : \n\n date du flux : \n"; 
		$mess .= duau2chaine($do['date']); 
		$mess .= "date en bdd :\n"; 
		$mess .= duau2chaine($ev->acc_date_lei() ); 

		// Si une modif de date est détecté, l'événement ne sera pas mis en actif. 
		$actif = FALSE; 

		alerte($ev->acc_id(), $mess, NON_VERIFIER, ALERTE_LEI_DATE ); 
		ajt_tc("$mess\n");
		insert_dateduau( $ev->acc_id(), $do['date']); 
	}
}

if( $modif )
{
	$ev->mut_source($SOURCE); 
	$ev->mut_titre($do['titre']); 
	$ev->mut_date_maj($do['date_maj']); 

	$ev->mut_desc( $do['com'] ); 
	$ev->mut_id_externe($do['id']); 

	/*
		Vérification pour mise en actif automatique
	*/

	// Trop de majuscule dans le titre
	if( ($pc = prcent_majuscule($do['titre']) ) > PRCENT_TITRE )
	{
		ajt_tc("Trop de lettre en majuscule dans le titre pour être activé. ($pc%)\n"); 
		$actif = FALSE; 
	}

	// Titre trop long 
	if( ($nb = strlen($do['titre']) ) > TAILLE_TITRE )
	{
		ajt_tc("Titre trop long pour être activé ($nb)."); 
		$actif = FALSE; 
	}
	
	// Trop de majuscule dans la description 
	if( ($pc =prcent_majuscule($do['combdd']) ) > PRCENT_DESC )
	{
		ajt_tc("Trop de lettre en majuscule dans la description pour être activé. ($pc%)\n"); 
		$actif = FALSE; 
	}

	// Description trop longue 
	if( ($nb = strlen($do['combdd']) ) > TAILLE_DESC )
	{
		ajt_tc("Description trop longue pour être activé ($nb)."); 
		$actif = FALSE; 
	}

	// Correspondance avec un contact 
	if( $id = recherche_contact($do['contact'], $SOURCE) )
	{
		$ev->mut_contact( array('id' => $id ) ); 
	}
	else
	{
		ajt_tc('Pas de correspondance pour l\'entité gestionnaire n°:'.$do['contact'].'.'); 
		if($do['ct_nom']) ajt_tc( 'nom : '.$do['ct_nom']); 
		if($do['ct_prenom']) ajt_tc( 'prenom : '.$do['ct_prenom']); 
		if($do['ct_tel']) ajt_tc( 'tel : '.$do['ct_tel']); 
		if($do['ct_site']) ajt_tc( 'site : '.$do['ct_site']); 
		ajt_tc("");
		$actif = FALSE; 
	}
	
	// Correspondance avec un lieu 
	if( $id = id_lieu($do['lieu'], $do['cp']) )
	{
		$ev->ajt_lieu(array('id' => $id, 'nom' => $do['lieu'] ) ); 
	}
	else
	{
		ajt_tc('Pas de correspondance pour ce lieu : '.$do['lieu'].' '.$do['cp'].".\n"); 	
		$actif = FALSE; 
	}

	// Corresopondance avec un thème 

	if( strpos($do['categorie_stq'], '#') )
	{
		ajt_tc('Plusieurs catégorie fournis : '.$do['categorie_stq'].'. Seul la première est utilisé.'."\n"); 
	}

	if( $id = id_theme($do['categorie'], $SOURCE) )
	{
		$ev->mut_categorie( array('id'=>$id, 'nom' => $do['categorie'] ) ); 

		if( !in_array( $id, id_theme_auto($SOURCE) ) )
		{
			ajt_tc('Le thème "'.$do['categorie'].'" n\'autorise pas la mise en actif automatique. '."\n"); 
			$actif = FALSE; 
		}
	}
	else
	{
		ajt_tc('Pas de correspondance pour ce thème : '.$do['categorie'].".\n"); 
		$actif = FALSE; 
	}


	// Extraction des dates  
	if( !duau2date( $do['date'], $ndate ) )
	{
		ajt_tc('Vérification des dates requise.'); 
		ajt_tc(duau2chaine($do['date']) ); 
		$actif = FALSE; 
	}

	$ev->mut_tab_date( $ndate ); 

	$ev->mut_etat( $actif ? 1 : 0 ); 

	$mode = $ev->acc_id() == 0;

	if( $mode )
	{ 
		ajt_tc('Ajout de l\'évenement (importation SIRTAQUI).'); 	
		$nbajt++;
	}
	else
	{
		ajt_tc('Modification de l\'évenement (importation SIRTAQUI).'); 	
		alerte($ev->acc_id(), acc_tc(), NON_VERIFIER, ALERTE_LEI_MODIF ); 
		$nbmod++;
	}  

	if( $actif ) 
	{
		ajt_tc('Mis en actif automatiquement.'); 
		$nbact++; 
	}

	event_lei_enr( $ev, acc_tc(), 968, $mode, $do); 
}
else
{
	maj_der_verif( $ev->acc_id() );
}

// Fin boucle principal 
}


if( ($nbevent == count($data['value']) && ($nbevent != 0) ) )
{
	$nbsup = supp_lei($SOURCE);
	$logsup = "Nb événement parser (".$nbevent.") et total (".count($data['value']).") correspondent, appel suppression\n"; 
}
else
{
	$logsup = "Nb événement parser (".$nbevent.") et total (".count($data['value']).") ne correspondent pas, pas de suppression\n"; 
	$nbsup = 0; 
}

file_put_contents('log-sirtaqui-sup.txt', $logsup, FILE_APPEND); 

stat_lei($nbevent, $nbajt, $nbmod, 0, $nbsup, $NB_REQ, $NB_PRE, $NB_EXE, 
	round( (microtime(TRUE)-TPS)*1000, 2), $nbact, $SOURCE );

echo 'Nb ajout : '.$nbajt."\n";
echo 'Nb modif : '.$nbmod."\n"; 
echo 'Nb actif : '.$nbact."\n"; 

echo $NB_REQ.'r, '. $NB_PRE .'p, '.$NB_EXE.'e'."\n"; 

var_dump($TAB_LOG); 
