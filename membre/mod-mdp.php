<?php
include '../include/init.php'; 

$code = $_GET['c'] ?? ''; 
$iden = $_GET['m'] ?? '';


$PAT->ajt_style('inscription.css', RETOUR.'membre/style/'); 

$modif_mdp = new liste_form(); 
$modif_mdp->ajt('mail', 'chaine', 'Identifiant', ['requis'=> TRUE, 'max'=>150, 'donne' => $iden]); 
$modif_mdp->ajt('code', 'chaine', 'Code de modification', ['requis'=> TRUE, 'max'=>150, 'donne' => $code ]); 
$modif_mdp->ajt('mdp', 'chaine', 'Nouveau Mot de passe', ['type' => 'password', 'requis'=> TRUE, 'min' =>INS_MIN_CAR]); 
$modif_mdp->ajt('mdp2', 'chaine', 'Confirmer le mot de passe', ['type'=> 'password', 'requis' => TRUE, 'min'=>INS_MIN_CAR] ); 

$valide = FALSE; 

if( isset($_POST['ok']) )
{
	$valide = TRUE; 
	$dm = $modif_mdp->donne(); 

	if( !$modif_mdp->verif() )
	{
		$valide=FALSE; 
	}

	if( $dm['mdp'] != $dm['mdp2'] )
	{
		$valide = FALSE; 
		$modif_mdp->acc('mdp2')->mess('Le mot de passe doit être identique.'); 
	}

	$donne = exepre('
		SELECT ID id 
		FROM Utilisateurs 
		WHERE User=? AND modif_mdp=? 
		AND date_modif_mdp+24*3600>?
		AND modif_mdp!=""
		LIMIT 1
	', 
		[$dm['mail'], $dm['code'], time() ]
	); 

	$do = fetch($donne); 

	if( !$do )
	{
		$valide = FALSE; 
		mess('Le code est dépassé ou incorrecte, ou l\'identifiant est incorrecte.'); 
	}

	if( $valide )
	{
		exepre('UPDATE Utilisateurs SET Pass=?, modif_mdp="", date_modif_mdp=0 WHERE ID=?',
			[md5($dm['mdp']), $do['id']]	
		);	

		mess('Votre mot de passe a été mis à jour.'); 
	}
}

include PATRON; 
