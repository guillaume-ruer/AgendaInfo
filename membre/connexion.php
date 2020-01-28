<?php
include '../include/init.php';
include C_INC.'reqa_class.php'; 
include C_INC.'article_fonc.php';
include C_INC.'article_class.php'; 
include C_ADMIN.'include/article_conf.php'; 

if($MEMBRE->connecte )
{
	article_const($ARTICLE_CONF); 

	$lsa = new ls_article($ARTICLE_CONF); 	
	$lsa->mut_type(NOUVELLE); 
	$lsa->pagin=FALSE; 
	$lsa->nbparpage=3; 
	$lsa = $lsa->req(); 
}

$titre = NOM_SITE.' : plateforme de diffusion des actualités de la Nouvelle-Aquitaine';

include HAUT_MEMBRE;
?>

<div id="alignement">

<p><img src="../img/logo_card_info_limousin.png" alt="agenda dynamique" width="400" height="155" hspace="0" 
	vspace="0" border="0" align="middle" longdesc="logo info limousin" />
</p>

  <?php pmess($MESS) ?>
  
<?php if(CONNECTE ) : ?>
  
<p>Bienvenue à l'adhérent : <?php echo $MEMBRE->pseudo ?></p>

	<?php if( droit( ADMIN ) ) : ?>
		<div id="entrer" >
			<p><a href="<?php echo C_ADMIN; ?>admin.php" >Accès à la plateforme de diffusion</a></p>
		</div>
	<?php endif  ?>



<?php else : ?>
<p>Une adhésion est nécessaire pour avoir un compte de diffusion.</p>
<form  action="<?php echo RETOUR; ?>membre/connexion.php" method="post" >
	  <table align="center" id="connexion_table" >
			<tr>
				<td align="right">Utilisateur (email pour les inscriptions en ligne)</td>
                <td> </td>
				<td><input type="text" name="pseudo" size="25" /></td>
			</tr>
			<tr>
				<td align="right">Mot de passe  </td>
                <td> </td>
				<td><input type="password" name="mdp" size="25" /></td>
			</tr>
		</table>
    <p><input type="submit" name="ok" value="Connexion" /></p>
	</form>
	
	<p><a href="https://www.asso.info-limousin.com/association/adhesion" >Adhésion</a> - <a href="oublie.php" >Mot de passe oublié</a> - <a href="<?php echo RETOUR; ?>index.php" >Retour à l'agenda</a></p>
	
<?php endif ?>

</div>
