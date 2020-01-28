<?php imp($lsevent->acc_requete() ) ?>

<?php $lsevent->acc_pagin()->affiche() ?>

<?php while( $e = $lsevent->parcours() ) : ?>  

	<?php imp($e) ?>

<?php endwhile ?>

<?php $lsevent->acc_pagin()->affiche() ?>

