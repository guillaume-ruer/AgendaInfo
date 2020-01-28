<?php

/*
	Fonction de connexion
*/

function connexion($login, $mdp)
{
	$login = trim($login);
	$mdp = trim($mdp);
	$connexion = FALSE;

	if(empty($login) OR empty($mdp) )
	{
		mess('Login ou mot de passe vide.');
		return FALSE;
	}

	$donne = req('SELECT id, droit, Pass AS mdp FROM Utilisateurs 
		WHERE User=\''.secubdd($login).'\' LIMIT 1 ');
	$do = fetch($donne);

	if( md5($mdp) == $do['mdp'] )
	{
		$id_joueur = (int)$do['id'];

		/*
			On modifie le nom de la session car les privilèges ont changé
		*/
		session_regenerate_id();
	
		/*
			Initilisation de toute les variables de SESSION
		*/

		$_SESSION['user_id'] = $id_joueur;
		$_SESSION['droit'] = (int)$do['droit'];
		$_SESSION['init'] = TRUE;	
	
		/*
			Le jeton servant contre le vol de session
		*/

		$jeton = sha1( uniqid() );
		setcookie('JETON', $jeton, time()+10*60, '/');
		$_SESSION['JETON'] = $jeton;
		
		/*
			MAJ du sessid pour vérifier qu'un seul utilisateur est sur le 
			même compte
		*/

		req('UPDATE Utilisateurs SET sessid = \''.session_id().'\' 
			WHERE id='.$id_joueur.' 
			LIMIT 1 ');

		/*
			On verifie la date de dernière modification de mot de passe 
		*/

		mess('Connexion réussie.');

		$connexion = TRUE;
	}
	else
	{
		/*
			T'as pas le bon pass/mot secret, attend un peu avant de 
			retenter
		*/

		sleep(1);
		mess('La connexion a échoué, veuillez réessayer.');
		$connexion = FALSE;
	}

	return $connexion;
}

/*
	Création d'un compte 
*/

function cree_compte($pseudo, $mdp, $mail)
{
	global $MESS;

	$erreur = FALSE;

	
	/*
		Vérification pseudo
	*/
	
	$donne = req('SELECT pseudo FROM Utilisateurs 
		WHERE pseudo=\''.secubdd($pseudo).'\' LIMIT 1' );
	$do = fetch($donne);

	if(!empty($do) )
	{
		$erreur = TRUE; 
		mess('Cet nom d\'utilisateur est déjà pris.');
	}
	
	if(preg_match('`[%,]`', $pseudo ) )
	{
		$erreur = TRUE; 
		mess('Les caractères ",", "%" ne sont pas autorisés dans votre nom d\'utilisateur.');
	}
	
	/*
		Vérification mail
	*/

	$mail = trim($_POST['mail']);

	if(!filter_var($mail, FILTER_VALIDATE_EMAIL) )
	{
		$erreur = TRUE; 
		mess("L'adresse email saisie n'est pas considérée comme valide.");
	}

	/*
		Pas d'erreur, on enregistre
	*/

	if(!$erreur)
	{
		$pseudo = secubdd($pseudo);
		$mail = secubdd($mail);
		$mdp = sha1($mdp);
		$code_conf = sha1( uniqid() );
		$time = time();
		
		req(" 	INSERT INTO Utilisateurs(pseudo, code_conf, mail, mdp, date_inscription,
				last_change_mdp)
			VALUES('$pseudo', '$code_conf', '$mail', '$mdp', $time, $time ) 
		");
		
		$id = $BDD->lastInsertId();  
	}
	else
	{
		$id = FALSE;
	}

	return $id; 
}
?>
