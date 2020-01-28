<?php
require '../../include/init.php'; 
require C_INC.'fonc_memor.php'; 

$def = [
	['Message de bienvenue',
	"<p>Bienvenu sur l'<strong>Agenda Dynamique</strong> du Limousin.<br />"
        ."Cette aide vous fera découvrir les fonctionnalitées pas à pas.<br />"
        ."L'objectif de cette page est de repartir avec <strong>votre planning au format pdf</strong> !</p>"
        ."<p>Cliquez sur &laquo;suivant&raquo; pour continuer.</p>"	
	],
	['La carte',
	"<p>La carte de la région.</p>"
	."<p>Un marqueur blanc est placé sur chaque ville où se trouve un événements de la liste de proposition. "
	."Un marqueur orange indique une ville où aura lieu un événement compris dans votre séléction."
	."Cliquez sur un marqueur pour afficher des informations concernant la ville.</p>"
	."<p>Pour obtenir les événements d'une commune, commencez à entrer son nom "
	."dans la barre de saisi au dessus de la carte. Validez votre choix en parcourrant "
	."les propositions à l'aide des flèches &laquo;haut&raquo; et &laquo;bas&raquo; de votre clavier "
	."et appuyé sur &laquo;Entrée&raquo;, ou cliquez sur le nom de la commune.</p>"
	."<p>Vous pouvez définir un périmètre autour de la commune sélectionnée à l'aide du menu déroulant &laquo;rayon&raquo;.</p>"
	."<p>Pour réinitialiser le filtre par commune, cliquez sur la croix à droite de &laquo;km&raquo;.</p>"
	],
	['Le calendrier',
	"<p>Le calendrier comportant le nombre d'événement correspondant aux filtre actuel en <span class='italic' >italic</span>.<br />"
	."Dépliez le calendrier à l'aide du bouton &laquo;<<<&raquo; pour avoir une vue d'ensemble.</p>"
	."<p>Cliquez sur une date pour filtrer par cette date.<br />"
	."&laquo;Cliquez-déposez&raquo; d'une date à une autre pour filtrer par cette plage de date.</p>"
	."<p>Les numéros des événements de votre séléction sont affichés les jours où ils ont lieux.</p>"
	],
	['Les thèmes',
	"<p>Vous pouvez activé ou désactivé les thèmes qui vous intéresse.<br />"
	."Survolez un thème avec votre souris pour faire apparaître sa description textuel.<br />"
	."Cliquez sur la première icone pour tout décocher.<br />"
	."Cliquez sur la deuxième icone pour tout cocher.</p>"
	],
	['La liste des propositions',
	"<p>La liste des événements correspondants aux filtres sélectionnés est affichée dans le premier onglet.<br />"
	."Un rappel des filtres actuels est affiché en premier. Cliquez sur la croix du filtre correspondant our le retirer.</p>"
	."<p>Cliquez sur le bouton &laquo;+&raquo; pour ajouter un événements à votre liste de séléction.</p>"
	],
	['La liste des sélections',
	"<p>Le deuxième onglet comprend les événements séléctionnée.</p>"
	."<p>Un numéro est attribué à chacun des événements séléctionné.<br />"
	."Ce numéro se retrouve dans le calendrier les jours où l'événement a lieu.</p>"
	."<p>Si un événement possède plusieurs dates, un bouton &laquo;Masquer des dates&raquo; vous donne la possibilité de masqué les dates "
	."que vous ne voulez pas voir apparaitre dans votre pdf finale.<br />"
	."Ce bouton va dérouler le calendrier et mettre les dates de l'événement en vert.<br />"
	."Cliquez sur les dates à masquer, elle deviendront rouge.</p>"
	."<p>Trois boutons apparaîtrons au dessus du calendrier : </p>"
	."<ul><li>&laquo;masquer toutes les dates&raquo; : pour rendre toute les dates en rouge (masqué)</li>"
	."<li>&laquo;Afficher toutes les dates&raquo; : pour rendre toute les dates en vert (affiché)</li>"
	."<li>&laquo;Valider&raquo; : pour terminer l'opération</li></ul>"
	],
	['Le bouton "générer mon pdf"',
	"<p>Vous avez terminé ? Cliquez ici pour obtenir votre planning en pdf !</p>"
	]
];

$phrase = rappel('dynagenda-phrase-aide', $def); 

if( isset($_POST['save_id'], $_POST['save_txt']) )
{
	$phrase[ (int)$_POST['save_id'] ][1] = $_POST['save_txt']; 
	memor('dynagenda-phrase-aide', $phrase); 
	exit(); 
}

$PAT->ajt_script('ckeditor/ckeditor.js'); 
$PAT->ajt_script('ckeditor/adapters/jquery.js'); 

require PATRON;
