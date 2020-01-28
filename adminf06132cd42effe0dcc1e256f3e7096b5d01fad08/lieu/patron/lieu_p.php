<h1>Gestion des lieux</h1>

<?php pmess() ?>

<?php for( $i = 0 ; $i < 26 ; $i++ ) : $c=sprintf("%c", $i+65); ?>
<a href="<?php echo NOM_FICHIER.'?l='.$c ?>" ><?php echo $c ?></a>
<?php endfor ?>

<ul>
<?php while( $l = $lieux->parcours() ) : ?>
	<li><?php echo $l->acc_nom() ?> (<?php echo $l->acc_dep()->acc_num() ?>)
		[<a href="lieu-form.php?id=<?php echo $l->acc_id() ?>" >Edit</a>]	
		[<a href="lieu.php?id=<?php echo $l->acc_id() ?>" onclick="return confirm('Voulez vous vraiment supprimer ce lieu ?')" 
			>Supprimer</a>]	
	</li>
<?php endwhile ?>
</ul>

<?php for( $i = 0 ; $i < 26 ; $i++ ) : $c=sprintf("%c", $i+65); ?>
<a href="<?php echo NOM_FICHIER.'?l='.$c ?>" ><?php echo $c ?></a>
<?php endfor ?>
