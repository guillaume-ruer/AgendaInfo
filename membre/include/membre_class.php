<?php

/*
*/

class membre
{
	public $id=0;
	public $login = '';
	public $droit = 0;
	public $connecte = FALSE;
	public $com = '';
	public $prenom = ''; 
	public $nom = ''; 
	public $mail=''; 
	public $mdp= ''; 
	public $id_structure=0; 
	public $compte_rendu=FALSE;
	public $notif=FALSE;

	function acc_login(){ return $this->login; }

	private function set($id, $login, $droit, $co, $com, $prenom, $nom, $idstr, $dc, $mail='', $cr=FALSE, $notif=FALSE )
	{
		$this->prenom = (string)$prenom; 
		$this->nom = (string)$nom;
		$this->id = absint($id);
		$this->pseudo = (string)$login;
		$this->connecte = (bool)$co;
		$this->com = (string)$com;
		$this->droit = (string)$droit; 
		$this->login = (string)$login; 
		$this->id_structure = (int)$idstr;
		$this->der_connexion = (int)$dc; 
		$this->mail = $mail; 
		$this->compte_rendu = (bool)$cr; 
		$this->notif = (bool)$notif; 
	}

	function der_connexion()
	{
		return empty($_SESSION['der_connexion']) ? '' : date('j/m/Y', $_SESSION['der_connexion']); 
	}

	function init($id)
	{
		$donne = req('
			SELECT id, prenom, nom, Commentaire com, droit, User login, Pass mdp, id_structure, der_connexion, 
				compte_rendu, email, notif
			FROM Utilisateurs 
			WHERE id='.absint($id).' 
			LIMIT 1 
		');

		if( $do = fetch($donne) )
		{
			$this->set($do['id'], $do['login'], $do['droit'], FALSE, 
				$do['com'], $do['prenom'], $do['nom'], $do['id_structure'], $do['der_connexion'], 
				$do['email'], $do['compte_rendu'], $do['notif'] ); 
			$this->mdp = $do['mdp']; 
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function session()
	{
		$session = TRUE;
		$id=0;

		if(isset($_SESSION['user_id']) )
		{
			$donne = req('
				SELECT Commentaire com, id, sessid, droit, User login, 
					nom, prenom, id_structure, der_connexion, email, 
					compte_rendu, notif
				FROM Utilisateurs 
				WHERE id='.(int)$_SESSION['user_id'].' 
				LIMIT 1 
			');

			$do = fetch($donne);

			if($do['sessid'] != session_id() )
			{
				$session = FALSE;
			}
		}	
		else
		{
			$session = FALSE;
		}

		if($session)
		{
			$this->set($do['id'], $do['login'], $do['droit'], TRUE, 
				$do['com'], $do['prenom'], $do['nom'], $do['id_structure'], 
				$do['der_connexion'],
				$do['email'], $do['compte_rendu'], $do['notif']); 
		}
		
		return $session; 
	}

	function connexion($login, $mdp)
	{
		global $TRACE; 

		$login = trim($login);
		$mdp = trim($mdp);
		$connexion = TRUE;

		if(empty($login) OR empty($mdp) )
		{
			mess('Login ou mot de passe vide.');
			return FALSE;
		}

		$donne = req('
			SELECT id, droit, Pass, User login, contact_id, email, 
				Commentaire com, prenom, nom, id_structure, der_connexion, compte_rendu,
				code_conf, notif
			FROM Utilisateurs 
			WHERE User=\''.secubdd($login).'\' 
			LIMIT 1 
		');
		$do = fetch($donne);

		if( $do['code_conf'] != '' )
		{
			mess('Votre mail n\'a pas encore été validé, veuillez suivre le lien dans le mail.'); 
		}
		elseif( md5($mdp) == $do['Pass'] )
		{
			$id = (int)$do['id'];

			$_SESSION['user_id'] = $id;

			$this->set( $do['id'], $do['login'], $do['droit'], TRUE, 
				$do['com'], $do['prenom'], $do['nom'], $do['id_structure'], $do['der_connexion'], 
				$do['email'], $do['compte_rendu'], $do['notif'] ); 
			$this->session_regen();
			$_SESSION['der_connexion'] = $do['der_connexion']; 
			req('UPDATE Utilisateurs SET der_connexion='.(int)time().' WHERE id='.absint($do['id']).' LIMIT 1 '); 

			mess('Connexion réussie.');
			$connexion = TRUE;
			$TRACE->insert('<strong>'.secuhtml($do['login']).'</strong> c\'est connecté.', T_CO ); 
		}
		else
		{
			sleep(1);
			mess('La connexion a échoué, veuillez réessayer.');
			$connexion = FALSE;
		}

		return $connexion;
	}

	function visiteur()
	{
		$this->set(0, '', 0, FALSE, '', '', '', 0, 0, '');
	}

	function deconnexion()
	{
		unset($_SESSION['JETON'], $_SESSION['user_id'] );
		setcookie('JETON', '', time()-3600 );
		$this->visiteur();
	}

	function cree_jeton($min=60)
	{
		$jeton = sha1( uniqid() );
		setcookie('JETON', $jeton, time()+$min*60, '/');
		$_SESSION['JETON'] = $jeton;
	}

	function session_regen()
	{
		session_regenerate_id();
		req('UPDATE Utilisateurs SET sessid=\''.session_id().'\' WHERE id='.(int)$this->id.' LIMIT 1 ');
	}

	function mut_mdp($mdp)
	{
		$this->mdp = md5($mdp); 
	}

	function mut_droit($droit)
	{
		if(is_array($droit) )
		{
			$this->droit=0; 
			foreach($droit as $num )
			{
				$this->droit |= $num; 
			}
		}
		else
		{
			$this->droit = $droit; 
		}
	}

	function insert()
	{
		global $BDD;
		$pre = prereq('INSERT INTO Utilisateurs(User, Pass, Commentaire, droit, nom, prenom, email, id_structure )
			VALUES(?,?,?,?,?, ?,?,?)'); 
		exereq($pre, array($this->login, $this->mdp, $this->com, $this->droit, $this->nom, $this->prenom, $this->mail, $this->id_structure ) ); 
		$this->id = $BDD->lastInsertId(); 
		mess('Création de l\'utilisateur réussi.'); 
	}

	function maj()
	{
		$tab = array($this->login, $this->com, $this->droit, $this->nom, $this->prenom, $this->mail, $this->id_structure );
		$champ = ''; 
		if(!empty($this->mdp ) )
		{
			$champ = ', Pass=?';
			$tab[] = $this->mdp; 
		}

		$tab[] = $this->id;

		$r = prereq('
			UPDATE Utilisateurs SET User=?, Commentaire=?, droit=?, nom=?, prenom=?, email=?, id_structure=?
			'.$champ.' 
			WHERE id=? LIMIT 1 
		');
		exereq($r, $tab ); 
		mess('Mise à jour de l\'utilsateur réussi.'); 
	}

	function enregistre()
	{
		if( empty($this->id) )
		{
			$this->insert();
		}
		else
		{
			$this->maj(); 
		}
	}

	function maj_public()
	{
		$r = prereq('UPDATE Utilisateurs SET email=?, compte_rendu=?, notif=? WHERE id=? '); 

		exereq($r, array(
			$this->mail,
			$this->compte_rendu,
			$this->notif, 
			$this->id,
		) ); 
	
		return TRUE; 
	}
}
