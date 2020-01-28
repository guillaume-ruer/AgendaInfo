<?php
require '../include/init.php'; 
require 'lei-v4_fonc.php'; 
require 'parcours_flux_class.php';
require C_INC.'evenement_class.php'; 
require 'evenement_lei_class.php'; 
require C_INC.'evenement_fonc.php'; 
require C_ADMIN.'lei/include/var-alerte.php'; 

define('PRCENT_TITRE', 30 ); 
define('PRCENT_DESC', 10 ); 
define('TAILLE_TITRE', 70);
define('TAILLE_DESC', 400 ); 
define('ECART_PAS_AUTO', 40 * 24 * 3600 ); 
define('LOCAL', TRUE);
define('LIGNE', FALSE); 

header('content-type: text/html; charset="utf-8"'); 

// Mettre LOCAL si maj_hash = TRUE
$maj_hash = FALSE; 

$flux = new parcours_flux(LIGNE); 
$flux->mut_mois(9); 
$flux->mut_modif(FALSE); 
$flux->mut_limite(10000); 
$verbeux = FALSE; 
$nbajt=0;
$nbmod=0;
$nbact=0; 
?>
<!DOCTYPE html >
<html>
<head>
	<meta http-equiv="content-type" content="text/html charset=utf-8" />
	<title>Liste des événements récupéré du lei</title>
	<link rel="stylesheet" type="text/css" href="../style/mode_dev.css" />
	<?php if($verbeux) : ?>
	<style type="text/css" >
	body
	{
		font-size:80%;
	}
	table
	{
		border-collapse:collapse;		
	}

	td
	{
		border:1px solid black;
	}
	.texte 
	{
		width:300px;
	}

	h3
	{
		
	}

	</style>
	<?php endif ?>
</head>
<body>
<?php if($verbeux) : ?>
<table>
	<tr>
		<th>Id</th>
		<th>Desc</th>
		<th>Lieu</th>
		<th>Catégorie</th>
		<th>Contact</th>
		<th>Code Postale</th>
		<th>Date</th>
	</tr>
<?php endif ?>
<?php while( $do = $flux->parcours() ) :  

$modif = FALSE; 
$actif = TRUE; 
vide_tc(); 

$ev = init_lei($do['id']); 

if( $maj_hash )
{
	if( $ev )
	{
		maj_hash($ev->acc_id(), $do['com'], $do['lieu'], $do['titre'], $do['categorie'], $do['contact'] );
	}

	continue; 
}

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
	$ev->mut_source(evenement::LEI); 
	$ev->mut_titre($do['titre']); 
	$desc_ajt = ''; 
	$desc_deb = ''; 
	if( !empty($do['adrprod_tel']) )
	{
		$desc_ajt .= ' Tél. : '.$do['adrprod_tel'].'.'; 
	}

	if(!empty($do['adrprod_url']) )
	{
		$desc_ajt .= ' Site : '.$do['adrprod_url'].'.';
	}

	if(!empty($do['adrprod_compl_adresse']) )
	{
		$desc_deb = $do['adrprod_compl_adresse'].'. ';
	}
	elseif(!empty($do['adrpec_compl_adresse']) )
	{
		$desc_deb = $do['adrpec_compl_adresse'].'. '; 
	}

	$ev->mut_desc( auto_format($desc_deb.$do['combdd']).$desc_ajt ); 
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
	if( $id = recherche_contact($do['contact']) )
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
		ajt_tc('Pas de correspondance pour ce lieu : '.$do['lieu'].".\n"); 	
		$actif = FALSE; 
	}

	// Corresopondance avec un thème 
	if( $id = id_theme($do['categorie']) )
	{
		$ev->mut_categorie( array('id'=>$id, 'nom' => $do['categorie'] ) ); 

		if( !in_array( $id, id_theme_auto() ) )
		{
			ajt_tc('Le thème "'.$do['categorie'].'" n\'autorise pas la mise en actif automatique. '); 
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
		ajt_tc('Ajout de l\'évenement (importation LEI).'); 	
		$nbajt++;
	}
	else
	{
		ajt_tc('Modification de l\'évenement (importation LEI).'); 	
		alerte($ev->acc_id(), acc_tc(), NON_VERIFIER, ALERTE_LEI_MODIF ); 
		$nbmod++;
	}  

	if( $actif ) 
	{
		ajt_tc('Mis en actif automatiquement.'); 
		$nbact++; 
	}

	event_lei_enr( $ev, acc_tc(), 414, $mode, $do); 
}
else
{
	maj_der_verif( $ev->acc_id() );
}


if($verbeux ) : 
?>
	<tr>
		<td><?php echo $do['id'] ?></td>
		<td class="texte" >
			<h3><?php echo $do['titre'] ?></h3>
			<p><?php echo $do['combdd'] ?></p>
		</td>
		<td><?php echo $do['lieu'] ?></td>
		<td><?php echo $do['categorie'] ?></td>
		<td><?php echo $do['contact'] ?></td>
		<td><?php echo $do['cp'] ?></td>
		<td><?php foreach($do['date'] as $date ) : ?>
			<?php echo $date['du'].' => '.$date['au'].' <br />'; ?> 
		<?php endforeach ?>
		</td>
		<td>
			<?php echo nl2br(acc_tc() )  ?>
		</td>
		<td>
			<?php imp($ev) ?>
		</td>
	</tr>
<?php 
endif; 
endwhile; 
if( $verbeux ) : 

?>
	</table>

<?php endif ?>

<?php 

if( ($flux->acc_nbevent() == $flux->acc_flux_nb_event() ) && ($flux->acc_flux_nb_event() != 0) )
{
	$nbsup = supp_lei();
	$logsup = "Nb événement parser (".$flux->acc_nbevent().") et total (".$flux->acc_flux_nb_event().") correspondent, appel suppression\n"; 
}
else
{
	$logsup = "Nb événement parser (".$flux->acc_nbevent().") et total (".$flux->acc_flux_nb_event().") ne correspondent pas, pas de suppression\n"; 
	$nbsup = 0; 
}

file_put_contents('log-lei-sup.txt', $logsup, FILE_APPEND); 

stat_lei($flux->acc_nbevent(), $nbajt, $nbmod, $flux->acc_nbelim(), $nbsup, $NB_REQ, $NB_PRE, $NB_EXE, 
	round( (microtime(TRUE)-TPS)*1000, 2), $nbact );
?>

<p><?php echo $flux->acc_nbevent() ?> événements. <?php echo $flux->acc_nbelim() ?> événements éliminé. 
	<?php echo $flux->acc_nberr() ?> événement inexploitable.</p>
<p>Nb ajout : <?php echo $nbajt ?>.<br />
Nb modif : <?php echo $nbmod ?>.<br />
Nb actif : <?php echo $nbact ?>.
</p>

<p><?php echo $NB_REQ ?>r, <?php echo $NB_PRE ?>p, <?php echo $NB_EXE ?>e</p>

<?php include C_PATRON.'mode_dev.php' ?>
</body>
</html>
