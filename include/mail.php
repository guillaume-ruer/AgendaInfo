<?php
function mon_mail($mail, $sujet, $txt, $html )
{
	/*
		VERIFICATION DES PARAMETRE D'ENTREE
	*/

	if(!filter_var($mail, FILTER_VALIDATE_EMAIL) )
	{
		return FALSE;
	}

	/*
		INITIALISATION
	*/

	//Nom 
	$exp = 'StrateGeyti';

	//Adresse 
	$adresse_exp = 'strategeyti@gmail.com';

	//Retour
	$retour = 'StrateGeyti';

	//Addresse retour 
	$adresse_retour = 'strategeyti@gmail.com';

	//Message au format texte 
	$txt = wordwrap($txt, 70); 

	//Message au format html
	$html = wordwrap($html, 70); 

	//Sujet :
	$sujet = $sujet; 

	/*
		TRAITEMENT 
	*/

	//Quel passage de ligne utilisé? Merci microsoft... 
	if (!preg_match('`^.+@(hotmail|live|msn).+$`', $mail))
	{
		$pl = "\r\n";
	}
	else
	{
		$pl = "\n";
	}

	/*
		Création de la boundary
	*/
	$boundary = '-----='.md5( rand() );

	/*
		Création de l'entete 
	*/
	$header = 'From: "'.$exp.'"<'.$adresse_exp.'>'.$pl;
	$header .= 'Reply-to: "'.$retour.'" <'.$adresse_retour.'>'.$pl; 
	$header .= 'MIME-Version: 1.0'.$pl; 
	$header .= 'Content-Type: multipart/alternative;'.$pl;
	$header .= ' boundary="'.$boundary.'"'.$pl;

	/*
		Création du message 
	*/

	$message = $pl.$boundary.$pl;
	$message .= 'Content-Type: text/plain; charset="UTF-8"'.$pl;
	$message .= 'Content-Transfer-Encoding: 8bit'.$pl;
	$message .= $pl.$txt.$pl;

	$message .= $pl.'--'.$boundary.$pl;
	
	$message .= 'Content-Type: text/html; charset="UTF-8"'.$pl;
	$message .= 'Content-Transfer-Encoding: 8bit'.$pl;
	$message .= $pl.$html.$pl;

	$message .= $pl.'--'.$boundary.'--'.$pl;
	$message .= $pl.'--'.$boundary.'--'.$pl;

	//Envoi du mail ! 
	mail($mail, $sujet, $message, $header);
}

function mon_mail2($mail, $sujet, $message )
{
	if(!filter_var($mail, FILTER_VALIDATE_EMAIL) )
	{
		return FALSE;	
	}


	//Retour
	$retour = 'StrateGeyti';

	//Addresse retour 
	$adresse_retour = 'strategeyti@gmail.com';
	
	//Nom 
	$exp = 'StrateGeyti';

	//Adresse 
	$adresse_exp = 'strategeyti@gmail.com';

	$pl = "\r\n"; 

	// Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
	$headers  = 'MIME-Version: 1.0' . $pl;
	$headers .= 'Content-type: text/html; charset=UTF-8' . $pl;

	// En-têtes additionnels
	$headers .= 'From: "'.$exp.'"<'.$adresse_exp.'>'.$pl;
	$headers .= 'Reply-to: "'.$retour.'" <'.$adresse_retour.'>'.$pl; 

	// Envoi
	return mail($mail, $sujet, $message, $headers);
}
?>
