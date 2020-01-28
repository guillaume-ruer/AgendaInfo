<?php

$def = [
	['Rappel', <<<START
Bonjour,

Votre renouvellement à l’adhésion à l’association Info Limousin arrive le mois prochain. 
Vous pouvez effectuer un règlement par carte bancaire dès à présent en vous connectant sur la plateforme de diffusion.
Sinon, une facture vous sera envoyée par email prochainement. 


 Association Info Limousin
  15, bld Victor Hugo 87120 Eymoutiers
  Tel : 09 77 84 02 55 (fixe adsl Orange)
  Email : contact@info-limousin.com
  Sites agenda :http://www.info-limousin.com et http://www.agenda-dynamique.com 
  Site association : http://www.asso.info-limousin.com 
START
	, 'Association Info Limousin : adhésion annuelle'
	],
	['Mail d\'adhésion', <<<START
Bonjour,

Vous êtes adhérent à l'association Info Limousin.

Veuillez trouver ci-joint une facture et les identifiants pour vous connecter à la plateforme de diffusion pour vérifier vos coordonnées, saisies d'annonces d'évènements, générés des flux d'agendas pour vos outils de communications, diffuser des affiches, partager votre compte…

Accès à la plateforme de diffusion : http://www.info-limousin.com
Utilisateur : {USER} 
Mot de passe : rendez-vous sur la plateforme en cas d’oubli

N'hésitez pas à nous contacter en cas de soucis ou questions.
Notice de la plate-forme : >http://minu.me/ah1w 

 Association Info Limousin
  15, bld Victor Hugo 87120 Eymoutiers
  Tel : 09 77 84 02 55 (fixe adsl Orange)
  Email : contact@info-limousin.com
  Sites agenda :http://www.info-limousin.com et http://www.agenda-dynamique.com 
  Site association : http://www.asso.info-limousin.com 

START
	, 'Association Info Limousin : adhésion'
	],
	['Mail de relance',<<<START
Bonjour,
 
Ceci est une relance automatique.
Sauf erreur de notre part, une facture est en attente de règlement concernant votre adhésion à l’association Info Limousin..
Vous pouvez régler par chèque bancaire, virement bancaire ou règlement en ligne par carte bancaire.

Si vous ne souhaitez pas reconduire votre adhésion, dites-le nous par retour d'email, nous désactiverons votre compte.

Après la deuxième relance, nous désactivons votre compte de diffusion.

Règlement  par chèque à l'ordre de : Association Info Limousin
Virement  bancaire à La banque Postale : 20041  / 01006 / 0660496G027 / 55
IBAN  : FR26 2004 1010 0606 6049 6G02 755 / BIC : PSSTFRPPLIM

Un agenda dynamique : http://agenda-dynamique.com

Application agenda dans votre téléphone et tablette sous Androïd 
http://www.asso.info-limousin.com/reseau-de-diffusion/application-android

Un agenda dans votre site Internet sous Joomla
http://www.asso.info-limousin.com/joomla/ilagenda

Un agenda dans votre site Internet sous WordPress
http://www.asso.info-limousin.com/reseau-de-diffusion/application-wordpress

Association Info Limousin
  15, bld Victor Hugo 87120 Eymoutiers
  Tel : 09 77 84 02 55 (fixe adsl Orange)
  Email : contact@info-limousin.com
  Sites agenda :http://www.info-limousin.com et http://www.agenda-dynamique.com 
  Site association : http://www.asso.info-limousin.com 

START
	, 'Association Info Limousin : attente de règlement'
	],
	['Mail de désactivation',<<<START
Bonjour, 

N’ayant pas de nouvelles de votre part quant au renouvellement de votre adhésion à l’association Info Limousin, nous désactivons votre compte sur la plateforme de diffusion, contactez-nous en cas de retard de règlement ou autre : contact@info-limousin.com.

Association Info Limousin
  15, bld Victor Hugo 87120 Eymoutiers
  Tel : 09 77 84 02 55 (fixe adsl Orange)
  Email : contact@info-limousin.com
  Sites agenda :http://www.info-limousin.com et http://www.agenda-dynamique.com 
  Site association : http://www.asso.info-limousin.com 
START
	,'Association Info Limousin : désactivation de votre compte de diffusion'
	]
];

foreach($def as $i => $d)
{
	$def[$i][1] = '<p>'.nl2br(lien_text($def[$i][1]) ).'</p>'; 
}

return $def; 
