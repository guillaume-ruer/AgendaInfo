<tr>
	<td><?php echo $id_prod ?></td>
	<td><?php echo $titre ?></td>
	<td><?php echo $com ?></td>
	<td><?php echo $type_nom ?></td>
	<td><?php echo $ent_ges ?></td>
	<td><?php echo contenu_element('ADRPROD_CP', $node) ?></td>
	<td>
		<ul>
		<?php if(!empty($tab_date ) ): ?>
			<?php foreach($tab_date as $date ) : ?>
				<li><?php echo $date ?></li>
			<?php endforeach ?>
		<?php endif ?>
		</ul>
	</td>
	<td>
		<ul>
		<?php if(!empty($date_duau) ) : ?>
			<?php foreach($date_duau as $duau ) : ?>
				<?php list($du, $au ) = $duau  ?>
				<li>du <?php echo $du ?> au <?php echo $au ?></li> 
			<?php endforeach ?>
		<?php endif ?>
		</ul>
	</td>
	<td>
		<ul>
		<?php if(!empty($date_duau_bdd) ) : ?>
			<?php foreach($date_duau_bdd as $duau ) : ?>
				<?php list($du, $au ) = $duau  ?>
				<li>du <?php echo $du ?> au <?php echo $au ?></li> 
			<?php endforeach ?>
		<?php endif ?>
		</ul>
	</td>
	<td><?php echo nl2br(historique2chaine() ) ?></td>
</tr>
