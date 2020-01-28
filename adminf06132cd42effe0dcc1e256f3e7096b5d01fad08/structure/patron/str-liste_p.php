<h1>Liste de la ou des structures</h1>
Vous pouvez partager votre "compte structure" avec d'autres, ainsi donner la possibilité à une autre structure de diffuser des événements en votre nom et inversement.

<?php pmess() ?>

<form action="str-liste.php" method="post" >
	<p>Recherche (nom de la structure) : <input type="text" name="rech" value="<?php echo $rech ?>" />
	<select name="ville" >
		<option value="" >Choisir une ville</option>
	<?php while( $v = $ls_ville->parcours() ) : ?>
		<option value="<?php $v->aff_id() ?>" 
			<?php selected( $v->acc_id() == $ville ) ?> ><?php $v->aff_nom() ?></option>
	<?php endwhile ?>
	</select>
	<input type="submit" name="ok" value="Ok !" />
	</p>
</form>

<?php $str->acc_pagin()->affiche() ?>

<table class="table_defaut" >
	<tr>
		<th>Num</th>
		<th>Nom</th>
		<th>Adresse</th>
		<th>Ville</th>
		<th>Dep</th>
		<th>Email</th>
		<th>Présentation</th>
		<th>Logo</th>
		<th>Date</th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<?php if(droit(GERER_UTILISATEUR) ) : ?>
		<th></th>
		<th></th>
		<?php endif ?>
	</tr>
<?php while($s = $str->parcours() ) : ?>
	<tr class="<?php echo ($s->acc_actif()==1) ? 'str_actif' : 'str_inactif' ?>" >
		<td><?php ps($s->acc_numero() ) ?></td>
		<td><?php ps( $s->acc_nom() ) ?></td>
		<td><?php ps( $s->acc_adresse()->acc_rue() ) ?></td>
		<td><?php ps( $s->acc_adresse()->acc_ville()->acc_nom() ) ?></td>
		<td><?php ps( $s->acc_adresse()->acc_ville()->acc_dep()->acc_num() ) ?></td>
		<td><?php ps( $s->acc_mail() ) ?></td>
		<td><?php ps(  strlen($s->acc_desc()) > 200 ? spesubstr($s->acc_desc(), 0, 200).' ...' : $s->acc_desc() ) ?>
		</td>
		<td>
			<?php if( $s->acc_logo() != '' ) : ?>
			<img src="<?php ps( C_IMG.'logos/'.$s->acc_logo() ) ?>" />
			<?php endif ?>
		</td>
		<td><?php echo date('d/m/Y', $s->acc_date() ) ?></td>
		<td><a href="str.php?ids=<?php ps( $s->acc_id() ) ?>&amp;p=<?php echo $p ?>" >Voir la structure</a></td>
		<td>
			<?php if(droit(GERER_UTILISATEUR) || $s->acc_droit() & STR_MODIFIER || $s->acc_id() == $MEMBRE->id_structure ) : ?> 
				<a href="str-form.php?ids=<?php ps( $s->acc_id() ) ?>&amp;p=<?php echo $p ?>" >Modifier la structure</a>
			</td>
			<td>
				<a href="../location/location.php?ids=<?php ps( $s->acc_id() ) ?>&amp;p=<?php echo $p ?>" >Relais</a>
			<?php endif ?>
		</td>
		<td>
			<?php if(droit(GERER_UTILISATEUR) || $s->acc_droit() & STR_DROIT || $s->acc_id() == $MEMBRE->id_structure ) : ?>
				<a href="str-droit.php?ids=<?php ps($s->acc_id()) ?>&amp;p=<?php echo $p ?>" >Accès aux droits</a>
			<?php endif ?>
		</td>
		
		<?php if(droit(GERER_UTILISATEUR) ) : ?>
			<td>
				<?php if($s->acc_actif()==1) : ?>
					<a href="str-liste.php?idsd=<?php ps( $s->acc_id() ) ?>&amp;p=<?php echo $p ?>" >Désactiver</a>
				<?php else : ?>
					<a href="str-liste.php?idsa=<?php ps( $s->acc_id() ) ?>&amp;p=<?php echo $p ?>" >Activer</a>
				<?php endif ?>
			</td>
			<td>
				<a href="str-liste.php?idssup=<?php ps( $s->acc_id() ) ?>&amp;p=<?php echo $p ?>"
					onclick="return confirm('Voulez vous vraiment supprimer cette structure ?')" >Supprimer</a>
			</td>
		<?php endif ?>
	</tr>
<?php endwhile ?>
</table>

<?php $str->acc_pagin()->affiche() ?>
