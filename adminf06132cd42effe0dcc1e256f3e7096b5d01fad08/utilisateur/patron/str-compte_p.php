<h1>Compte</h1>

<form action="str-compte.php" method="get" >
	<p>Recherche (nom/convention) : <input type="text" name="nom" value="<?php echo $nom ?>" />

	Type : <select name="type" >
		<option value="-1" >Tous</option>
	<?php foreach(structure::$tab_type as $id => $t) : ?>
		<option value="<?php echo secuhtml($id) ?>" <?php selected($id == $type) ?>><?php echo secuhtml($t) ?></option>
	<?php endforeach ?>
	</select>

	<label>Payant : <input type="checkbox" name="payant" value="1" <?php checked($str_payant) ?> /></label>

	<input type="submit" name="ok" value="Recherche !" />
	<?php if( $bt_reinit ) : ?>
		<a href="str-compte.php" >Ré-initialise</a>
	<?php endif ?>
	</p>
</form>

<p>
<?php for($i=1; $i<=$nb_page; $i++) : ?>
	<a href="<?php echo str_replace('%pg', $i, $url_pagin) ?>" ><?php echo $i ?></a>
<?php endfor ?>
</p>

<table id="str-table" >
	<thead>
		<tr>
			<th>Numéro</th>
			<th>Infos</th>
			<th>Fin d'adhésion</th>
			<th>Etat relance</th>
			<th colspan="6" >Action</th>
		</tr>
	</thead>
	<tbody>
<?php while($do = fetch($donne) ) : ?>
	<tr data-id="<?php echo (int)$do['id'] ?>" class="<?php echo structure::$tab_class_etat[ $do['actif'] ] ?>" >
		<td><?php echo (int)$do['numero'] ?></td>
		<td>Nom : <?php echo secuhtml($do['nom']) ?><br />
		Type : <?php echo structure::text_type($do['type']) ?>
		<?php if($do['payant']) : ?>
		Payant : <?php echo structure::cout($do['type']) ?>€
		<?php else : ?>
		Gratuit
		<?php endif ?>
		<br />

		Convention : <?php echo secuhtml($do['conv']) ?><br />
		Email : <?php echo secuhtml($do['email']) ?><br />
		<a href="<?php echo C_ADMIN ?>structure/str-form.php?ids=<?php echo (int)$do['id'] ?>" target="_blank" >Editer la structure</a> 
		</td>
		<td><a class="maj-date-bt" href="#" ><span class="date-adh" >
			<?php if( empty($do['date_fin_adhesion'] ) ) : ?>
				Non définis
			<?php else : ?>
				<?php echo date('d/m/Y', $do['date_fin_adhesion']) ?>
			<?php endif ?>
			</span></a>
		</td>
		<td>
			<?php if( $do['rappel'] != 0 ) : ?>
				<?php echo $tab_rappel[ $do['rappel'] ] ?><br />
				<?php if( !empty($do['rappel_facture']) ) : ?>
					<a href="<?php echo C_ADMIN.'utilisateur/facture.php?f='.$do['rappel_facture'] ?>" >Facture en attente</a><br />
				<?php endif ?>
				<br />
			<?php endif ?>

			<?php 
				if( !empty($do['date_fin_adhesion']) ) 
				{
					$nbj = (int)( (time() - $do['date_fin_adhesion']) / (24*3600) );

					if( $nbj < 0 )
					{
						echo abs($nbj). ' jours avant la fin de l\'adhésion.'; 
					}
					else
					{
						echo '<span class="evidence" >'.abs($nbj). ' jours passé depuis la fin de l\'adhésion.</span>'; 
					}
				}
			?>
		</td>
		<td>
			<?php foreach(structure::$tab_etat as $ide => $etat ) : ?>
				<label><?php echo $etat ?> : 
					<input class="active" type="radio" 
						name="str_etat_<?php echo (int)$do['id'] ?>" 
						value="<?php echo $ide ?>" 
						<?php checked($ide == $do['actif']) ?>
				/></label><br />
			<?php endforeach ?>
		</td>
		<td><input type="button" class="str-facture" value="Factures" /></td>
	</tr>
	<tr class="str_infos" >
		<td colspan="20" >
		</td>
	</tr>
<?php endwhile ?>
	<tbody>
</table>

<p>
<?php for($i=1; $i<=$nb_page; $i++) : ?>
	<a href="<?php echo str_replace('%pg', $i, $url_pagin) ?>" ><?php echo $i ?></a>
<?php endfor ?>
</p>


