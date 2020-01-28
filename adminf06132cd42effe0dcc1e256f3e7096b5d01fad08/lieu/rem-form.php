<?php
require '../../include/init.php'; 
require_once C_INC.'ls_contact_class.php';
require_once C_INC.'remarquable_fonc.php';

$PAT->ajt_style('rem-form.css',  C_ADMIN.'lieu/style/'); 

$liste_contact = new liste_contact();
$liste_contact->fi_structure = droit(GERER_EVENEMENT) ? 0 : $MEMBRE->id;
$contact = $liste_contact->requete();

$tab = []; 

while($c = $contact->parcours() )
{
	$tab[ $c->id ] = $c->nom.','.$c->titre.' ('.$c->ville.')'; 
}

$form = new liste_form(); 
$form->ajt('titre', 'chaine', 'Titre'); 
$form->ajt('desc', 'texte', 'Description', ['rows' => 8, 'cols' =>80] ); 
$form->ajt('lat', 'chaine', 'Latitude'); 
$form->ajt('long', 'chaine', 'Longitude'); 

$onglet = new onglet_form(['defaut' => 'interne']); 

$contact = new liste_form(); 
$contact->ajt('mail', 'mail', 'Email'); 
$contact->ajt('tel', 'tel', 'Téléphone'); 
$contact->ajt('site', 'chaine', 'Site internet'); 

$onglet->ajt('interne', $contact, 'Saisi interne'); 

$externe = new liste_form(); 
$externe->ajt('contact', 'deroulant', 'Contact ', ['tab_option' => $tab] );

$onglet->ajt('externe', $externe, 'Contact structure');

$form->ajt('contact', $onglet); 
$form->ajt('adr', 'chaine', 'Adresse'); 
$form->ajt('ville', 'barre_proposition', 'Ville', ['fichier'=> 'lieu.php', 'class' => 'ville', 'label' => 'Commune ', 'limite' => 1] ); 
$form->ajt('id', 'cache'); 

$remarquable = new remarquable(); 
$valide = FALSE; 

if( isset($_POST['ok']) )
{
	if( $form->verif() )
	{
		$donne = $form->donne(); 

		if( $donne['contact']['onglet'] == 'interne' )
		{
			$donne = array_merge($donne, $donne['contact']['donne']); 
			unset($donne['contact']); 
		}
		elseif( $donne['contact']['onglet'] == 'externe') 
		{
			$donne['contact'] = new contact(['id' => $donne['contact']['donne']['contact'] ]); 
		}

		$donne['type'] = $_POST['type']; 
		
		if( !empty($donne['ville']) )
		{
			$donne['ville'] = $donne['ville'][0]; 
		}

		$remarquable = new remarquable($donne); 

		crud_enr($remarquable); 
		$valide = TRUE; 
	}
}
elseif( isset($_GET['id']) )
{
	if( $rem = remarquable_init($_GET['id']) )
	{
		$remarquable = $rem; 
		// init form avec donnée 
		$donne['titre'] = $remarquable->titre(); 
		$donne['desc'] = $remarquable->desc(); 
		$donne['adr'] = $remarquable->adr(); 
		$donne['lat'] = $remarquable->lat(); 
		$donne['long'] = $remarquable->long(); 
		$donne['id'] = $remarquable->id(); 

		if( $remarquable->ville()->acc_id() != 0 )
		{
			$donne['ville'] = [
				$remarquable->ville() 
			];
		}

		if( $remarquable->contact()->acc_id() != 0 )
		{
			$donne['contact'] = [
				'onglet' => 'externe', 
				'donne' => ['contact' => $remarquable->contact()->acc_id() ]
			];
			$onglet->defaut = 'externe'; 
		}
		else
		{
			$donne['contact'] = [
				'onglet' => 'interne', 
				'donne' => [
					'tel' => $remarquable->tel(),
					'mail' => $remarquable->mail(), 
					'site' => $remarquable->site() 
				]
			];		
		}

		$form->mut_donne($donne); 
	}
}

require PATRON; 
