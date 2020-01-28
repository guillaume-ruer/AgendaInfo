<?php

$tab_type=[]; 

foreach(structure::$tab_type as $id => $val )
{
	$tab_type[] = [$id, $val] ; 
}

/*
$tab_type = array(
	array('non_adherent', 'non adhérent' ),  
	array('particulier', 'particulier' ),
	array('particulier_forfait','particulier forfait'),
	array('particulier_ gratuit','particulier gratuit'),
	array('particulier_gratuit','particulier bienfaiteur'),
	array('particulier_honoraire','particulier honoraire'),
	array('association', 'association'),
	array('association_forfait','association forfait'),
	array('association_gratuit','association gratuit'),
	array('association_bienfaiteur','association bienfaiteur'),
	array('association_honoraire','association honoraire'),
	array('collectivite','collectivité'),
	array('collectivite_forfait','collectivité forfait'),
	array('collectivite_gratuit','collectivité gratuit'),
	array('collectivite_bienfaiteur','collectivité bienfaiteur'),
	array('collectivite_honoraire','collectivité honoraire'),
	array('societe','société'),
	array('societe_forfait','société forfait'),
	array('societe_gratuit','société gratuit'),
	array('societe_bienfaiteur','société bienfaiteur'),
	array('societe_honoraire','société honoraire')
);
*/ 
