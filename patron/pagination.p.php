<p class="pagination" >
	<?php for($i=0; $i < $lsevent->nombre_page AND $i< 20 ; $i++ ) : ?>
		<a href="<?php echo_url_pagin($lien, $i); ?>" class="<?php echo $i == $page ? 'actif' : 'inactif' ?>" ><?php echo $i+1 ?></a>
	<?php endfor ?>
</p>

