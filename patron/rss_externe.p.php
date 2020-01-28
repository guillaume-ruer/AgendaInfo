<?php while( $ev = $lsevent->parcours() ): ?>
	<item>
		<title><?php echo $ev->date.' - '.$ev->titre.' - ' ?>
		<?php foreach($ev->tab_ville as $v ) : ?>
			<?php echo $v->virg.' '.$v->nom.' ('.$v->dep.')' ?>	
		<?php endforeach ?>
		</title>
		<description><?php echo $ev->description ?> Contact : <?php echo $ev->adh_nom.' '.$ev->adh_titre.' '.$ev->source ?> 
		</description>
		<link ><?php echo $PAT->val('baseurl') ?></link>
	</item>
<?php endwhile ?>
