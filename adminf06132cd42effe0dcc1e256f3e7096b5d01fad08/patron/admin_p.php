<h1>Accueil de l'outil de diffusion <?php if(!empty($MEMBRE->id_structure) ) : echo ' - '.$str->acc_nom(); endif ?></h1>


<div id="col-gauche" class="block" >
	<h2>Informations</h2>

	<p id="bar_bouton" >Nbre d'évènements à venir dans l'agenda (chiffre global) : <strong><?php echo $nb_event; ?></strong><br />
	Nbre d'évènements de votre structure à venir : <strong><?php echo str_nbe_a_venir($str) ?></strong><br />
	Nbre d'évènements saisis par l'utilisateur pendant l'année en cours : <?php echo date('Y') ?> : <strong><?php echo $nb_cree_membre ?></strong><br />
	Nbre d'évènements modifiés dans l'année en cours : <strong><?php echo $nb_modif_membre ?></strong><br /><br />

	<?php if( !$str ) : ?>
		Vous n'avez pas de structure principale
	<?php elseif( $str->acc_code_externe() ) : ?>
		Mon flux RSS : <strong><?php echo ADD_SITE.'externe/'.$str->acc_code_externe().'/0_0_FR.rss' ?></strong>
	<?php else : ?>
		Créez un relais dans votre structure et choisissez le dans la liste pour l'activer. 
	<?php endif ?>
	<br /><br />
	Dernière connexion à l'outil : <strong><?php echo $MEMBRE->der_connexion() ?><br />
	</p>
	<p>
	<?php if( $str !== FALSE ) : ?>
		Date de l'adhésion de votre structure : <strong><?php $str->aff_date(1) ?></strong><br /><br />
		<a href="<?php echo C_ADMIN ?>structure/str.php?ids=<?php $str->aff_id() ?>" >Renouveler mon adhésion</a>
	<?php endif ?>
	</p>

</div>
<div id="col-droite" class="block" >
	<h2>Actualité</h2>

	<div id="actualite" >
	<?php
        article_const($ARTICLE_CONF); 

        $lsa = new ls_article($ARTICLE_CONF);   
        $lsa->mut_type(NOUVELLE); 
        $lsa->pagin=FALSE; 
        $lsa->nbparpage=3; 
        $lsa = $lsa->req(); 
        article_affiche($ARTICLE_CONF, $lsa);
	?>
	</div>
</div>

<div  class="table_defaut" >

	<table>
		<caption >
		Mes derniers événements créés 
		<?php if( MODE_DEV ) : echo '('.$mes10der->acc_tps().')'; endif ?>
		</caption>
		<tr>
			<th>Titre</th>
			<th>Date </th>
			<th>Compte</th>
			<th>Etat</th>
			<th>Action</th>
		</tr>
		<?php while($do = $mes10der->parcours() ) : ?>

		<tr class="<?php echo $mes10der->acc_switch() ? 'ligne1' : 'ligne2' ?>" >
			<td class="titre"><?php $do->aff_titre(100) ?></td>	
			<td><?php ps( $do->acc_date_creation() ) ?></td>	
			<td><?php ps( $do->acc_createur()->acc_nom() ) ?></td>	
			<td><?php $do->aff_etat() ?></td>	
			<td><a href="<?php echo C_ADMIN ?>evenement/event-form.php?id_maj=<?php echo $do->acc_id() ?>" >Modifier</a></td>	
		</tr>
		<?php endwhile ?>
  </table>
</div>
<br />
<br />
<div class="table_defaut">
	<table >
		<caption >
		Mes derniers événements modifiés
		<?php if( MODE_DEV ) : echo '('.$mes10dermod->acc_tps().')'; endif ?>
		</caption>
		<tr>
			<th>Titre</th>
			<th>Date </th>
			<th>Compte</th>
			<th>Etat</th>
			<th>Action</th>
		</tr>
		<?php while($do = $mes10dermod->parcours() ) : ?>

		<tr class="<?php echo $mes10dermod->acc_switch() ? 'ligne1' : 'ligne2' ?>" >
			<td class="titre"><?php $do->aff_titre(100) ?></td>	
			<td><?php $do->aff_date_modif() ?></td>	
			<td><?php ps( $do->acc_modifieur()->acc_nom() ) ?></td>	
			<td><?php $do->aff_etat() ?></td>	
			<td><a href="<?php echo C_ADMIN ?>evenement/event-form.php?id_maj=<?php echo $do->acc_id() ?>" >Modifier</a></td>	
		</tr>
		<?php endwhile ?>
  </table>
</div>
<br />
<br />
<div class="table_defaut"  >

	<table >
		<caption >Les derniers événements saisis
		<?php if( MODE_DEV ) : echo '('.$der20->acc_tps().')'; endif ?>
		</caption>
		<tr>
			<th>Titre</th>
			<th>Date </th>
			<th>Compte</th>
			<th>Etat</th>
			<?php if( droit(GERER_EVENEMENT) ) : ?>
			<th>Action</th>
			<?php endif ?>
		</tr>
		<?php while($do = $der20->parcours() ) : ?>
		<tr class="<?php echo $der20->acc_switch() ? 'ligne1' : 'ligne2' ?>" >
			<td class="titre"><?php $do->aff_titre(100) ?></td>	
			<td><?php ps( $do->acc_date_creation() ) ?></td>	
			<td><?php ps( $do->acc_createur()->acc_nom() ) ?></td>	
			<td><?php $do->aff_etat() ?></td>	
			<?php if(droit(GERER_EVENEMENT) ) : ?>
				<td><a href="<?php echo C_ADMIN ?>evenement/event-form.php?id_maj=<?php echo $do->acc_id() ?>" >Modifier</a></td>	
			<?php endif ?>
		</tr>
		<?php endwhile ?>
	</table>
</div>
<br />
<br />
<div class="table_defaut">
	<table >
		<caption >
		Les derniers événements modifiés
		<?php if( MODE_DEV ) : echo '('.$der20mod->acc_tps().')'; endif ?>
		</caption>
		<tr>
			<th>Titre</th>
			<th>Date </th>
			<th>Compte</th>
			<th>Etat</th>

			<?php if( droit(GERER_EVENEMENT) ) : ?>
				<th>Action</th>
			<?php endif ?>
		</tr>
		<?php while($do = $der20mod->parcours() ) : ?>

		<tr class="<?php echo $der20mod->acc_switch() ? 'ligne1' : 'ligne2' ?>" >
			<td class="titre"><?php $do->aff_titre(100) ?></td>	
			<td><?php $do->aff_date_modif() ?></td>	
			<td><?php ps( $do->acc_modifieur()->acc_nom() ) ?></td>	
			<td><?php $do->aff_etat() ?></td>	
			<?php if(droit(GERER_EVENEMENT) ) : ?>
				<td><a href="<?php echo C_ADMIN ?>evenement/event-form.php?id_maj=<?php echo $do->acc_id() ?>" >Modifier</a></td>	
			<?php endif ?>
		</tr>
		<?php endwhile ?>
	</table>
</div>


<div style="clear:both" ></div>
