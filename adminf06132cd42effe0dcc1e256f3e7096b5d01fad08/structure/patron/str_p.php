<h1><?php $str->aff_nom() ?></h1>

<?php if( $str->acc_logo() ) : ?>
	<p>Logo : <img src="<?php echo C_IMG ?>logos/<?php ps( $str->acc_logo() ) ?>" alt="<?php ps( $str->acc_logo() ) ?>" width="160px"  height="90px" /></p>
<?php endif ?>

<?php if( $str->acc_banniere() ) : ?>
	<p>Bannière : <img src="<?php echo C_IMG ?>bandeaux/<?php ps( $str->acc_banniere() ) ?>" alt="<?php ps( $str->acc_banniere() ) ?>" width="120px"  height="60px" /></p>
<?php endif ?>

<p>Type : <?php echo $strtype ?><br />

Numéro de structure : <?php ps( $str->acc_numero() ) ?>.<br /> Adhésion le <?php $str->aff_date(1) ?>. </p>

<p><?php if( $str->acc_actif() == 1 ) : ?>
	<strong>Compte de diffusion activé : les saisies sont affichées.</strong>
<?php elseif( $str->acc_actif() == 0 ) : ?>
	<strong>Compte de diffusion  désactivé et non activable par paiement, veuillez adresser votre demande de réactivation auprès de l'association Info Limousin.</strong>
<?php else : ?>
	<strong>Compte de diffusion  désactivé en attente de réglement de l'adhésion : les saisies sont masquées.</strong>
<?php endif ?>
</p>
<h2>Adresse</h2>

<p><?php ps( $str->acc_adresse()->acc_rue() ) ?></p>
<p><?php ps( $str->acc_adresse()->acc_ville()->acc_cp().' '.$str->acc_adresse()->acc_ville()->acc_nom() ) ?></p>
<p>Mail : <?php ps($str->acc_mail() ) ?></p>
<p><?php ps($str->acc_facebook() ) ?></p>

<h2>Description</h2>
<p> <?php ps( $str->acc_desc() ) ?> </p>

<h2>Contacts</h2>
<ul id="ls_contact" >
	<?php foreach($str->acc_tab_contact() as $c ) : ?>
		<li><?php ps( $c->acc_titre() ) ?>
			<?php ps( $c->acc_tel() ) ?>
			<?php ps( $c->acc_site() ) ?>
		</li>
	<?php endforeach ?>
</ul>



<?php if( $cout_annuel > 0 ) : 
// On affiche le renouvellement seulement si il y a quelque chose à payer. 
?>

<h2>Mon adhésion</h2>

<p>Pour renouveler son adhésion à l'association (ouvrant un accès à la saisie sur la plateforme),<br />
régler le montant annuel par chèque bancaire envoyé au siège de l'association,  faire un virement bancaire, ou faire un réglement en ligne par carte bancaire par Paypal.
</p>

<?php if($gerant ) : ?>
<p>Les rappels :<br />
  - 
  un email un mois avant la date anniversaire, <br />
  - un email
  à la date anniversaire,<br />
  - un email de relance
 les deux mois suivants.<br />
  Le troisième mois suivant la date anniversaire, si le renouvellement n'a pas été effectué, la diffusion des saisies faites par la structure sera suspendue.
	  <br />
    Vous pouvez activer ou désactiver les rappels par email automatique.	</p>

	<?php if( $rappel ) : ?>
<a href="str.php?ids=<?php $str->aff_id() ?>&rp=0" >Cliquer ici pour ne plus  recevoir des emails de rappel automatique</a>
<?php else : ?>
<p>
		<a href="str.php?ids=<?php $str->aff_id() ?>&rp=1" >Cliquer ici pour recevoir des emails de rappel automatique</a><br />
		</p>

  <?php endif ?>
  
  <?php endif ?>
<h2>Récapitulatif</h2>

<table>
	
	<?php if( $str->payant() ) : ?>
	<tr>
		<td>Adhésion annuelle : <strong><?php echo structure::text_type( $str->acc_type() ) ?></strong></td>
		<td>&nbsp;</td>
		<td><?php echo structure::cout( $str->acc_type() ) ?>€</td>
  </tr>
	<?php endif ?>

	<?php foreach($tab_opt as $opt ) : ?>
		<tr>
			<td><?php $opt->aff_description()  ?></td>
			<td>&nbsp;</td>
			<td><?php $opt->aff_prix() ?>€</td>
		</tr>
	<?php endforeach ?>
	<tr>
		<td><strong>Total</strong></td>
		<td>&nbsp;</td>
		<td><strong><?php echo $cout_annuel ?>€</strong></td>
	</tr>
</table>
<h2>Renouvellement</h2>
<p>1. Adhésion en ligne avec Carte bancaire : <br />
  <a href="http://www.asso.info-limousin.com/association/adhesion" target="_blank">http://www.asso.info-limousin.com/association/adhesion </a><br />
  <br />
  2. Bon adhésion imprimable avec règlement par chèque à l'ordre de &quot;association Info Limousin&quot; : <br />
  <a href="https://drive.google.com/drive/folders/0B6oWMgL-qv0bMTBlNmY1YTktM2Q2Yy00YmM0LWI1NDMtYTQ2ZmM4NDY4Y2Vj" target="_blank">https://drive.google.com/drive/folders/</a> <br />
<br />
  3. Virement bancaire La Banque Populaire Aquitaine Centre Atlantique<br />
  IBAN  : FR76 1090 7002 7296 0218 3157 130 / BIC : CCBPFRPPBDX</p>

<br />
<p>Association Info Limousin - 15, bld Victor Hugo 87120 Eymoutiers<br />
Email : <a href="mailto:contact@agenda-dynamique.com">contact@agenda-dynamique.com</a><br />
<br />
</p>

<?php 
// Fin infos renouvellement
endif ?>
