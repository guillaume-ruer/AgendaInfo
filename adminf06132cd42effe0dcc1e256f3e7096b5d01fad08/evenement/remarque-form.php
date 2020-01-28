<?php
require_once '../../include/init.php'; 
require_once C_INC.'mail_fonc.php';  
require_once C_INC.'evenement_fonc.php';
require_once C_INC.'evenement_class.php';
require_once C_INC.'adresse_class.php'; 
require_once C_INC.'ville_class.php'; 
require_once C_INC.'departement_class.php'; 
require_once C_INC.'categorie_class.php'; 
require_once C_INC.'tarif_class.php'; 
require_once C_INC.'fonc_memor.php'; 
require_once C_INC.'structure_class.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'reqa_class.php'; 
require_once 'include/phrase_mail_fonc.php'; 

function deb_ligne($chaine, $char )
{
	return $char.str_replace("\n", "\n".$char, wordwrap( trim($chaine), 65, "\n") ); 
}

non_autorise(GERER_UTILISATEUR);

http_param(array('ide' => 0 ) );

$traitement = FALSE; 

if( isset($_POST['ok']) )
{
	/*
		Traitement du mail à envoyé 
	*/
	http_param(array('mail' => '', 'rq_contenu' => '', 'sujet' => '' ) ); 
	$mail = trim($mail).', adhesion@info-limousin.com'; 
	mel($mail, $sujet, $rq_contenu);
	mess('Email de remarque envoyé.'); 	

	$trace = "<p>Destinataire : ".secuhtml($mail)."</p>
	<p>Sujet : ".secuhtml($sujet)."</p>
	<p>".nl2br(secuhtml($rq_contenu) )."</p>";

	$TRACE->insert( $trace, T_MAIL ); 
	$traitement = TRUE; 
}

if( !$traitement )
{
	if( ($event = event_init($ide) ) === FALSE )
	{
		mess('Cet événement n\'existe pas.'); 
		page_erreur();
	}

	$char = "|\t"; 
	$haut = rappel('mail-haut'); 
	$bas = rappel('mail-bas'); 
	$sujet = rappel('mail-sujet'); 
	$rq_contenu = trim($haut)."\n\n"; 
	$rq_contenu .= deb_ligne($event->acc_titre()."\n\n".$event->acc_desc(), $char); 

	if(isset($_GET['okrq']) )
	{
		$dm = event_der_modif($event); 
		$rq_contenu .=  "\n\nAvant modération par info-limousin : \n\n";

		if( !empty($dm['titre']) OR !empty($dm['desc']) )
		{
			$rq_contenu .= deb_ligne($dm['titre']."\n\n".$dm['desc'],$char);
		}
		else
		{
			$rq_contenu .= deb_ligne($dm['event'], $char ); 
		}
	}
	$rq_contenu .= "\n\n".trim($bas); 

	$str = $event->acc_contact()->acc_structure(); 
	$mail = $str->acc_mail_rq() == '' ? $str->acc_mail() : $str->acc_mail_rq(); 
	
	$PAT->ajt_script('phrase_mail.js'); 
	$liste_phrase = liste_phrase(); 
	while( $do = $liste_phrase->parcours() )
	{
		$tab_phrase[] = array('id' =>$do->id, 'phrase' => $do->phrase, 'dim' => $do->dim ); 
	}
}

require PATRON; 
