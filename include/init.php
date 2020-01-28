<?php

/*
	Fichier d'initialisation, appeler en premier sur chaque fichier du site
	Par StrateGeyti
*/

//Afin de calculer le temps de génération de la page 
define('TPS', microtime(TRUE) );

//Affichage des erreur php
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');

//Afin de pouvoir utiliser les SESSION, pour les connexion
session_start();

//constante de serveur
$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$ip_local = '192.168.1.28'; 

define('EN_LOCAL', $http_host == $ip_local); 
define('ADD_SITE', ( EN_LOCAL ? 'http://'.$ip_local.'/www/infolimo/' : 'http://'.$http_host.'/' ) );
define('ADD_SITE_DYN', ( EN_LOCAL ? 'http://'.$ip_local.'/www/infolimo/agenda-dynamique/' : 'http://agenda-dynamique.com/' ) );

//Recherche du nombre de retour dossier à effectué pour revenir à la racine, depuis le fichier apelant. 
$debug = debug_backtrace();
define('FICHIER', basename($debug[0]['file'], '.php') ); 
define('NOM_FICHIER', basename($debug[0]['file']) ); 
$nbst = substr_count( $debug[0]['file'] , '/') + 1 - substr_count( __FILE__ , '/');

if($nbst < 0 ) 
{
	//Si c'est négatif, y'a un problème ! 
	exit('Problème de configuration du site.');
}
else
{
	//Sinon, on défini la constante RETOUR
	define('RETOUR', str_repeat('../', $nbst ) );
}

//Définition de constantes de chemin
define('C_STYLE', RETOUR.'style/');
define('C_DOS_PHP', RETOUR.'dos-php/');
define('C_INC', RETOUR.'include/');
define('C_FORM', C_INC.'form/');
define('C_FORM_PAT', C_INC.'form/pat/');
define('C_PATRON', RETOUR.'patron/');
define('D_ADMIN', 'adminf06132cd42effe0dcc1e256f3e7096b5d01fad08/'); 
define('C_ADMIN', RETOUR.D_ADMIN );
define('C_JAVASCRIPT', RETOUR.'javascript/');

define('HAUT', C_PATRON.'haut.php');
define('BAS', C_PATRON.'bas.php');
define('C_IMG', RETOUR.'img/');
define('C_DESIGN', C_IMG.'design/');
define('C_BOUTON', C_IMG.'bouton/');
define('C_EVENT_IMAGE' , C_DOS_PHP.'event_image/'); 
define('C_LIEN_IMG', C_DOS_PHP.'lien_img/'); 

//Constantes de droit 
$tab_droit = array(
	'ADMIN'=>0x1, 
	'CHANGER_FOND'=>0x4, 
	'PREFIX'=>0x20, 
	'GERER_LEI'=>0x40, 
	'MODIF_ETAT'=>0x80, 
	'GERER_EVENEMENT'=>0x100, 
	'TOUT_STAT'=>0x200,
	'GERER_UTILISATEUR'=>0x800,
	'GERER_ARTICLE' => 0x1000,
	'GERER_SYMBOLE' => 0x2,
	'GERER_VISUEL' => 0x2000,
	'GERER_LIEU' => 0x4000, 
);

foreach($tab_droit as $droit => $i)
{
	define($droit, $i );
}

define('NB_DROIT', count($tab_droit) ); 

// Droit spécifique au structures 
define('STR_EVENEMENT', 1);
define('STR_MODIFIER', 2);
define('STR_DROIT', 4);

/*
	Fonctions souvent utilisé. 
*/

require C_INC.'fonc.php';
require C_INC.'fonc_sql.php';
require C_INC.'fonc_infolimo.php'; 
require C_INC.'objet_base_class.php';
require C_INC.'identifiant_class.php'; 
require C_INC.'id_nom_class.php'; 
require C_INC.'reqo_class.php'; 
require C_INC.'parcours_reqo_class.php';
require C_INC.'pagin_reqo_class.php'; 
require C_INC.'crud_fonc.php'; 
require C_INC.'crud_class.php'; 

set_include_path( get_include_path().PATH_SEPARATOR.C_INC.PATH_SEPARATOR.C_FORM); 

spl_autoload_register(function($class){ 
	require_once $class.'_class.php'; 
});  

//Variable de configuration
include C_INC.'conf.php'; 

/*
// MODE_DEV à TRUE uniquement dans l'admin : 
if( ( $v =  strpos($debug[0]['file'], 'adminf06132cd42effe0dcc1e256f3e7096b5d01fad08' ) ) !== FALSE )
{
    $dev = TRUE; 
}
*/

define('MODE_DEV', $dev); 
error_reporting(MODE_DEV ? E_ALL : 0 ); 

try
{
	$BDD= new PDO('mysql:host='.$host.';dbname='.$bdd, $utilisateur , $mdp );
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
}

//On les supprime juste au cas où.
unset($host, $bdd, $utilisateur, $mdp); 

//On indique au serveur de base de donnée que nos retour doivent être en utf8
$BDD->query('SET NAMES utf8'); 
$BDD->query('SET sql_mode=""'); 


//Définition des constantes de configuration
define('NOM_SITE', $nom_site);

/*
	Système de traçage  
*/

include C_INC.'trace_conf.php';
include C_INC.'trace_fonc.php';
include C_INC.'trace_class.php'; 

$TRACE = new trace('trace', $TRACE_CONF); 
$TRACE->conf2def(); 

/*
	Gestion des patron
*/

define('PATRON', C_INC.'patron.php'); 
require 'patron_class.php'; 

$PAT = new patron; 
$PAT->mut_titre('Info Limousin');  
$PAT->ajt_style('style.css'); 
$PAT->ajt_script('fonc.js'); 

/*
	Gestion des connexions 
*/

include RETOUR.'membre/include/membre_class.php';
$MEMBRE = new membre;

if(isset($_GET['dec']) )
{
	$MEMBRE->deconnexion(); 
}
elseif(isset($_POST['mdp'], $_POST['pseudo']) )
{
	$MEMBRE->connexion($_POST['pseudo'], $_POST['mdp']);
}
else
{
	$MEMBRE->session(); 
}

define('CONNECTE', $MEMBRE->connecte );
define('ID', $MEMBRE->id );
define('DROIT', $MEMBRE->droit );

/*
	Include automatique des fichiers 'init_module.php'
	( en partant de la racine vers le dossier actuel) 
*/

$nbs = substr_count( RETOUR, '/');

for($i = $nbs-1; $i >= 0; $i-- )
{
	$inclure = str_repeat('../', $i ).'include/init_module.php';

	if(file_exists($inclure) )
	{
		include $inclure;
	}
}

/*
	Gestion des variable d'environement pour les langues
*/

//Récupération de l'id de langue. 
$l = 1;

if(isset($_GET['l']) ) 
{
	$tab_langue = array(1=>'FR', 2=>'EN' ); 
	$l = ($cle = array_search($_GET['l'], $tab_langue) ) ? $cle : (int) $_GET['l'] ; 
}
elseif(isset($_POST['l']) )
{
	$l = (int)$_POST['l']; 
}

$l = 1;

define('CODE_LANGUE', langue($l) );
setlocale(LC_ALL, CODE_LANGUE); 
putenv('LANG='.CODE_LANGUE); 
putenv('LANGUAGE='.CODE_LANGUE); 
define('ID_LANGUE', $l) ;
date_default_timezone_set('Europe/Paris'); 

//Inclusion du fichier de langue. 
$tab_fichier_langue = array(1 => 'francais.php', 2 => 'english.php' );  
include RETOUR.'lang/'.( ( isset($tab_fichier_langue[ $l ]) ) ? $tab_fichier_langue[ $l ] : $tab_fichier_langue[ 1 ] ) ; 

// Autre constantes 
$tab = array('LOC_CAT', 'LOC_THEME', 'LOC_LIEUX', 'LOC_GRPLIEUX', 'LOC_CONTACT', 'LOC_STR' ); 
$i=0x1; 

foreach($tab as $nom )
{
	define($nom, $i); 
	$i<<=1; 
}

if( MODE_DEV){
	$PAT->ajt_style('mode_dev.css');
}

