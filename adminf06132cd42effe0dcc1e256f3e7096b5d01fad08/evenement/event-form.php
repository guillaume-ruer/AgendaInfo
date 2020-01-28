<?php
//init 
require '../../include/init.php'; 
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'evenement_class.php'; 
require_once C_INC.'evenement_fonc.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'adresse_class.php'; 
require_once C_INC.'categorie_class.php'; 
require_once C_INC.'ls_contact_class.php';
require_once C_INC.'tarif_class.php'; 
require_once C_INC.'alerte_class.php'; 
require_once C_INC.'ls_alerte_class.php'; 
require_once C_INC.'alerte_fonc.php'; 
require_once C_ADMIN.'lei/include/var-alerte.php'; 
require_once C_INC.'fonc_memor.php'; 
require_once C_INC.'public_class.php'; 
require_once C_INC.'html_lib.php'; 

define('NB_JOUR_VISIBLE', 120 ); 
define('NB_JOUR_CALENDRIER', 60 );

define('IMAGE_LARGEUR', 160);
define('IMAGE_HAUTEUR', 226); 

define('MIN_TITRE', 4 ); 
define('MIN_DESC', 10); 

define('MAX_NB_DATE', 124); 

// Calendrier
$dos = RETOUR.'JSCal2/';
$PAT->ajt_style('css/jscal2.css', $dos);
$PAT->ajt_style('css/border-radius.css', $dos);

$PAT->ajt_script('js/jscal2.js', $dos ); 
$PAT->ajt_script('js/lang/fr.js', $dos ); 

// Autre script 
$PAT->ajt_script('ajax.js');
$PAT->ajt_script('base.js');
$PAT->ajt_script('formatage.js');

$commentaire = "modification de l'événement"; 
$affiche_formulaire=TRUE; 
$affiche_remarque = FALSE; 

/*
	Formulaire affiche
*/

$img_affiche = new fichier_form(array('dos' => C_IMG.'bandeaux/', 'hauteur' => 232, 'largeur' => 164, 'pdf2jpg' => TRUE) ); 
$image = new fichier_form(array('dos' => C_EVENT_IMAGE, 'format'=>'jpg,jpeg', 'hauteur'=> IMAGE_HAUTEUR, 'largeur' => IMAGE_LARGEUR ) ); 

http_param(array( 'id_maj' => 0 ) ); 

$event = new evenement(array(
	'id' => $id_maj
)); 

// #On vérifie que l'utilisateur à le droit de géré l'événement en question 
if( $id_maj != 0 && !event_membre_droit($id_maj, $MEMBRE) )
{
	page_erreur(); 	
}

// Champ de séléction de lieu 
$ch_lieu = new barre_proposition_form(['fichier'=> 'lieu.php', 'class' => 'ville', 'label' => 'Commune '] ); 

//Traitement de l'événement 
if(isset($_POST['ok'] ) || isset($_POST['okrq']) )
{
	$valide = TRUE; 
	/*
		Validation du formulaire 
	*/

	http_param(array('date' =>'', 'etat' => 0, 'sym' => 0, 'titre' => '', 
		'desc' => '', 'contact' => 0, 'etat' => 0, 
		'com' => '', 'tarif' => 0, 
		'tab_alerte' => array(), 'public' => 0,
		'affiche_url'=>'', 
		'stq_date_maj' => 0
	) );

	// Dates 
	$date = trim($date);
	$date = rtrim($date, ',' );
	$date = explode(',', $date); 
	$event->mut_tab_date($date); 
	$event->mut_date_maj($stq_date_maj); 

	if( count( $event->acc_tab_date() ) == 0 )
	{
		$valide = FALSE; 
		mess('Veuillez sélectionner au moins une date.'); 
	}
	elseif( count($event->acc_tab_date() ) >= MAX_NB_DATE)
	{
		$valide = FALSE; 
		mess('Veuillez sélectionner moins de '.MAX_NB_DATE.' dates.'); 
	}

	// Titre 
	$titre = trim($titre); 
	if( strlen($titre) < MIN_TITRE )
	{
		$valide=FALSE; 
		mess('Le titre doit faire plus de '.MIN_TITRE.' caractères.') ; 
	}
	$event->mut_titre($titre); 

	// Description 
	$desc = trim($desc); 

	if( strlen($desc) < MIN_DESC )
	{
		$valide=FALSE; 
		mess('La description doit faire plus de '.MIN_DESC.' caractères.'); 
	}
	$event->mut_desc($desc); 

	//Initialisation des propriété

	if( empty($sym) || $sym == 45 )
	{
		$valide=FALSE; 
		mess('Veuillez sélectionner une catégorie.'); 
	}

	$event->mut_categorie( new categorie( array('id' => $sym) ) ); 
	$event->mut_tarif( new tarif( array('id' => $tarif ) ) ); 
	$event->mut_public( array('id'=>$public) ); 
	$do_image = $image->donne(); 
	$event->mut_image( $do_image['nom'] ); 

	// Affiche
	$do_affiche = $img_affiche->donne(); 
	$event->mut_affiche( $do_affiche['nom'] ); 
	$event->mut_affiche_url( $affiche_url ); 

	if( droit(MODIF_ETAT) )
	{
		$event->mut_etat($etat);
	}
	else
	{
		if($etat == 1 )
		{
			mess("Vous n'avez pas les droits pour mettre un événement en actif.");	
			$event->mut_etat(0); 
		}
		else
		{
			$event->mut_etat($etat);
		}
	}

	/*
		#Gérer le droit sur le contact 
	*/

	if( empty($contact) )
	{
		$valide = FALSE;
		mess('Veuillez sélectionner un contact.');
	}

	$event->mut_contact( new contact(array('id' => $contact) ) ); 

	// Lieux 

	$do_lieu = $ch_lieu->donne(); 

	foreach($do_lieu as $lieu )
	{
		$event->ajt_lieu( $lieu ); 	
	}

	if( count($event->acc_tab_lieu() ) < 1 )
	{
		$valide = FALSE; 
		mess('Veuillez sélectionner au moins un lieu.'); 
	}

	if( $valide )
	{
		alerte_verifier( $tab_alerte ); 
		$com = trim($com); 
		$event = event_enr($event, $com); 
		$affiche_formulaire = FALSE; 


		if(isset($_POST['okrq']) )
		{
			$affiche_remarque=TRUE; 
			$addr = 'remarque-form.php?okrq=1&ide='.$event->acc_id(); 
			header('Location: '.$addr  ); 
			mess('Redirection php <a href="'.$addr.'" >Remarque</a>'); 
			page_erreur(); 
		}
	}
}
elseif( !empty($_GET['id_maj']) )
{
	/*
		Arrivé sur la page avec un identifiant 
	*/
	if( ($event = event_init($_GET['id_maj'] ) ) === FALSE )
	{
		mess('Identifiant de l\'événement inconnu.'); 
		page_erreur(); 
	}
}
else 
{
	$commentaire = "Ajout de l'événement"; 
}

$historique = event_historique($event); 

/*
	Formulaire 
*/
include C_INC.'chrono_class.php'; 

$prefixe = new reqa('
	SELECT prefixe, id 
	FROM prefixe_event 
	ORDER BY prefixe
'); 

$c = new chrono; 

$tps = microtime(TRUE); 

//Requete pour les symboles 
$symbole = new reqa('SELECT absint::CAT_ID AS id , secuhtml::CAT_NAME_FR AS nom FROM Categories ORDER BY nom');

//contact 
$liste_contact = new liste_contact();
$liste_contact->fi_structure = droit(GERER_EVENEMENT) ? 0 : $MEMBRE->id;
$contact = $liste_contact->requete(); 

//Tableau pour les menu déroulant 
$tab_etat = array( 1 => 'actif' , 0=>'inactif', 2=>'supprimé' ); 
$tab_tarif = array( 'Gratuit', 'Participation libre', 'Non indiqué', 'Payant, Gratuit', 'Payant'); 
$tab_tarif_img = array( 'e-gratuit.jpg', 'e-participation-libre.jpg', 'e-pas-indique.jpg', 'e-payant-gratuit.jpg', 'e-payant.jpg'); 

// Liste des alertes 
$alerte = new ls_alerte( array( 'idevent' => $event->acc_id() ) ); 
$alerte->requete(); 

$ch_lieu->mut_donne( $event->acc_tab_lieu() ); 

/*
	Variable dans l'entête du document html 
*/
$titre = ''; 
$intitule = $event->acc_id() == 0 ? ' Ajouter un ' : 'Edition d\'un '; 

$img_affiche->mut_nom($event->acc_affiche() ); 
$image->mut_nom( $event->acc_image() ); 

$affiche_url = $event->acc_affiche_url(); 

require PATRON; 
