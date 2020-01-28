<h1>Gestion des groupes de lieux</h1>

<p><a href="grp-lieu-form.php" >Ajouter un groupe de lieu</a></p>

<?php pmess() ?>

<ul>
<?php while( $g = $grp_ls->parcours() ) : ?>
	<li><?php echo $g->acc_nom() ?> 
	<?php if( $g->acc_num() ): ?>
		(<?php $g->aff_num() ?>)
	<?php endif ?>
		[<a href="grp-lieu-form.php?id=<?php echo $g->acc_id() ?>" >edit</a>]
		[<a href="grp-lieu.php?id=<?php echo $g->acc_id() ?>"
			onclick="return confirm('Voulez vous vraiment supprimer ce groupe ?')" 
			>Supprimer</a>]
	</li>
<?php endwhile ?>
</ul>

<?php $grp_ls->acc_pagin()->affiche() ?>
