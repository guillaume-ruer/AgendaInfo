<?php $sep =FALSE; ?>

<?php while($a = $lsa->parcours() ) : ?>

	<?php if($sep) : ?>
		<hr />
	<?php else : $sep=TRUE; endif ?>
	
	<h3 class="article_titre" ><?php echo $a->titre ?> 
		<span class="article_commentaire_lien" >
		<?php if($a->commentaire ) : ?>
			
		<?php endif ?>
		<?php if( article_membre_droit($ARTICLE_CONF, $a->type, $a->id_createur, $MEMBRE->id, $MEMBRE->droit) ) : ?>
			[<a href="<?php echo C_ADMIN ?>article/article-form.php?ida=<?php echo $a->id ?>&amp;t=<?php echo $a->type ?>" >Modifier</a>]
		<?php endif ?>
		</span>
	</h3>

	<?php if( !empty($ARTICLE_CONF[$a->type]['etat'] ) ) : ?>
		<p><?php echo $ARTICLE_CONF[$a->type]['etat'][$a->etat] ?></p>
	<?php endif ?>

	<p class="article_contenu" ><?php echo $a->article ?></p>
	<p class="article_auteur" >Auteur : <?php echo $a->pseudo ?> le <?php echo $a->date ?></p>

<?php endwhile ?>
