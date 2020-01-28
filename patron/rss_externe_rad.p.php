<?php while( $ev = $lsevent->parcours() ): ?>
	<item>
		<title><?php echo $ev->date; ?></title>
		<description><?php echo $ev->titre; ?> -
		<?php foreach($ev->tab_ville as $v ) : ?>
			<?php echo $v->virg.' '.$v->nom.' ('.$v->dep.')' ?>	
		<?php endforeach ?>
		</description>
		<link target="_parent">http://rad.info-limousin.com/agenda-du-reseau.html</link>
	</item>
<?php endwhile ?>
