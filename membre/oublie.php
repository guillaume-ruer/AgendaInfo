<?php
require_once '../include/init.php' ;
require_once C_INC.'courriel_class.php'; 

$PAT->ajt_style('inscription.css', RETOUR.'membre/style/'); 

$identifiant = isset($_POST['mail']) ? trim($_POST['mail']) : NULL; 

if( !empty($identifiant) )
{
	$donne = exepre('SELECT ID id, User, date_modif_mdp, email FROM Utilisateurs WHERE User=:mail LIMIT 1',
		[ 'mail' =>$identifiant ]
	); 

	if( $do = fetch($donne) )
	{
		if( $do['date_modif_mdp'] +24*3600 < time() )
		{
			$code = code_aleat(20);
			exepre('UPDATE Utilisateurs SET modif_mdp=?, date_modif_mdp=? WHERE id=?',
				[$code, time(), $do['id']]	
			);		

			// mail avec le code de modif. 
			$mail = ''; 

			if( filter_var($do['User'], FILTER_VALIDATE_EMAIL) )
			{
				$mail = $do['User']; 
			}
			elseif( filter_var($do['email'], FILTER_VALIDATE_EMAIL) )
			{
				$mail = $do['email']; 
			}
			else
			{
				mess('Aucune adresse disponible'); 
			}

			if( !empty($mail) )
			{
				$c = new courriel; 
				$c->exp = 'contact@info-limousin.com'; 
				$c->dest = $mail; 
				$url = ADD_SITE.'membre/mod-mdp.php?m='.$do['User'].'&c='.$code; 
				$c->html = <<<START
<p>Bonjour, </p>	

<p>Voici votre code de confirmation pour changer de mot de passe : "$code".</p>

<p>Veuillez suivre ce lien pour modifier votre mot de passe : 
<a href="$url" >$url</a>.</p>

<p>Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email.</p>

<p>A bientôt,<br />
L'association Info Limousin
</p>
START;
				$c->sujet = 'Info-Limousin : Code de modification de votre mot de passe.'; 
				$c->envoie(); 
				
				mess('Un email a été envoyé à votre adresse.');
			}
		}
		else
		{
			mess('Veuillez patienter avant de demander un nouveau code.'); 
		}
	}
}

include PATRON;
