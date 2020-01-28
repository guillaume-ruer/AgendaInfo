<?php
include '../include/init.php'; 
include C_INC.'fonc_memor.php'; 
include C_INC.'courriel_class.php'; 


if( !rappel('inscription') )
{
	mess('L\'inscription est fermé pour le moment, veuillez revenir plus tard.'); 
	$ins_ouvert = FALSE; 
}
else
{
	$ins_ouvert = TRUE; 
}


$PAT->ajt_style('inscription.css', RETOUR.'membre/style/'); 
$PAT->ajt_style('javascript/jquery-ui-1.11.4.custom/jquery-ui.min.css', RETOUR);

$PAT->ajt_script('jquery.js'); 
$PAT->ajt_script('jquery-ui-1.11.4.custom/jquery-ui.min.js');
$PAT->ajt_script('inscription.php', RETOUR.'membre/javascript/'); 

if( $ins_ouvert )
{
	$f_perso = new liste_form();
	$f_perso->ajt('nom', 'chaine', 'Nom', ['requis'=> TRUE, 'max'=>150]); 
	$f_perso->ajt('prenom', 'chaine', 'Prénom', ['requis'=> TRUE, 'max'=>150]); 

	$f_connexion = new liste_form(); 
	$f_connexion->ajt('mail', 'mail', 'Adresse mail', ['requis'=> TRUE, 'max'=>150]); 
	$f_connexion->ajt('mdp', 'chaine', 'Mot de passe', ['type' => 'password', 'requis'=> TRUE, 'min' =>INS_MIN_CAR]); 
	$f_connexion->ajt('mdp2', 'chaine', 'Confirmer le mot de passe', ['type'=> 'password', 'requis' => TRUE, 'min'=>INS_MIN_CAR] ); 

	$f_structure = new liste_form(); 
	$f_structure->ajt('type', 'deroulant', 'Type de structure', [
		'tab_option'=> structure::$tab_type, 
		'requis' => true,
		'exp' => 'Détermine le montant de l\'adhésion annuel'
	]); 
	$f_structure->ajt('nom', 'chaine', 'Nom de la structure', ['requis'=> TRUE]); 
	$f_structure->ajt('titre', 'chaine', 'Titre du contact', ['exp'=>'ex: Office, secrétariat...']); 
	$f_structure->ajt('tel', 'tel', 'Téléphone', ['requis'=> TRUE]); 
	$f_structure->ajt('url', 'url', 'Site Internet'); 

	$f_adresse = new liste_form(); 
	$f_adresse->ajt('ville', 'chaine', 'Commune', ['nom' => 'ch_lieu', 'requis' => TRUE ] ); 
	$f_adresse->ajt('ville_id', 'cache', NULL, ['nom' => 'ville_id'] ); 
	$f_adresse->ajt('adresse', 'chaine', 'Rue', ['requis' => TRUE] ); 

	$valide = FALSE; 

	if( isset($_POST['ok']) )
	{
		$valide = TRUE; 

		$dp = $f_perso->donne(); 
		$dc = $f_connexion->donne(); 
		$ds = array_merge($f_structure->donne(), $f_adresse->donne() ); 

		if( !$f_perso->verif() || !$f_connexion->verif() || !$f_structure->verif() || !$f_adresse->verif() )
		{
			$valide = FALSE; 
		}

		if( $dc['mdp'] != $dc['mdp2'] )
		{
			$valide = FALSE; 
			$f_connexion->acc('mdp2')->mess('Le mot de passe doit être identique.'); 
		}

		// Unicité du mail 
		$donne = prereq('SELECT ID FROM Utilisateurs WHERE User LIKE ? LIMIT 1'); 
		exereq($donne, [$dc['mail']]); 

		if($do= fetch($donne) )
		{
			$valide = FALSE; 
			$f_connexion->acc('mail')->mess('Cette adresse mail est déjà enregistré. Connecté vous après avoir valider l\'adresse en suivant le lien reçu dans le mail.'); 
		}

		if( $valide )
		{
			// Enregistrement et envoie du mail
			$ville = $ds['ville']; 
			$id_ville = $ds['ville_id'];
			$adresse = $ds['adresse']; 

			if( empty($id_ville) )
			{
				// Id de la ville non transmis, on vérifie l'existence de la ville 
				$ville_exists = exepre('SELECT Lieu_ID id FROM Lieu WHERE Lieu_Ville LIKE(?)', [$ville]); 

				if( $v = fetch($ville_exists) )
				{
					$id_ville = $v['id']; 
				}
				else
				{
					// Si la ville n'existe pas, on l'insert
					exepre('INSERT INTO Lieu(Lieu_Ville) VALUES(?)', [$ville]); 
					$id_ville = derid(); 
				}
			}

			$pre_str = prereq('INSERT INTO structure(nom, type,date_adhesion,actif,payant,email,date_fin_adhesion,ville,adresse)VALUES(?,?,?,?,?, ?,?,?,?)'); 
			exereq($pre_str, [ $ds['nom'], $ds['type'], time(), 2, 1, $dc['mail'], time(), $id_ville, $adresse ] ); 
			$id_str = derid(); 

			$pre_ct = prereq('INSERT INTO structure_contact(id_structure, site, tel, titre)VALUES(?,?,?,?) '); 
			exereq($pre_ct, [$id_str, $ds['url'], $ds['tel'], $ds['titre']]); 
			$id_ct = derid(); 

			$code_conf = code_aleat(16); 

			$pre = prereq('INSERT INTO Utilisateurs(User, Pass, uActif,droit,nom,prenom, email,code_conf, id_structure)VALUES(?,?,?,?,?, ?,?,?,?)'); 
			exereq($pre, [ $dc['mail'], md5($dc['mdp']), 1, 1, $dp['nom'], $dp['prenom'], $dc['mail'] , $code_conf,$id_str ] ); 
			$id_util = derid(); 

			$pre_droit = prereq('INSERT INTO structure_droit(utilisateur,structure,droit,rappel)VALUES(?,?,?,1)');
			exereq($pre_droit, [$id_util, $id_str, 7]); 

			// Envoie du mail avec code conf. 
			debug($code_conf); 
			$mail = new courriel(); 
			$mail->exp = 'contact@info-limousin.com'; 

			$url = ADD_SITE.'membre/verif_code.php?m='.$dc['mail'].'&c='.$code_conf;

			$mail->dest = $dc['mail']; 
			$mail->sujet = 'Info-Limousin : Confirmation de votre adresse mail';

			$mail->html = '
			<p>Bonjour,</p>
			
			<p>Pour activer votre compte, veuillez cliquer sur ce lien : 
			<a href="'.$url.'" >Activer votre compte</a></p>
			
			<p>Sinon, copier cette url dans votre barre d\'adresse : '.$url.'</p>

			<p>A bientôt.<br />
			L\'équipe Info-Limousin.
			</p>
			';

			$mail->texte = '
			Bonjour, 

			Voici l\'url à suivre pour confirmer votre adresse mail : "'.$url.'".

			A bientôt. 
			L\'équipe Info-Limousin.
			';

			$mail->envoie(); 


			/*
				Notification par mail 
			*/

			$donne = req('SELECT email FROM Utilisateurs WHERE notif!=0'); 

			while($do = fetch($donne) )
			{
				$mail = new courriel(); 	
				$mail->dest = $do['email']; 
				$mail->exp = 'contact@info-limousin.com'; 
				$mail->sujet = 'Nouvelle inscription'; 
				$mail->html = '
				<p>Bonjour,</p>

				<p><strong>'.$dp['nom'].' '.$dp['prenom'].'</strong> viens de s\'inscrire sur la plateforme.</p>

				<p>Sa structure est : <strong>'.$ds['nom'].'</strong> de type '.structure::text_type($ds['type']).'.</p>
				'; 

				debug($mail); 
				$mail->envoie(); 
			}
		}
	}
}

include PATRON; 
