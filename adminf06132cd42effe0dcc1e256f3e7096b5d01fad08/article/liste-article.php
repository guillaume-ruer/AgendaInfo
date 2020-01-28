<?php
include '../../include/init.php'; 
include C_INC.'reqa_class.php'; 
include C_INC.'article_class.php'; 

ajt_style('article.css'); 
article_const($ARTICLE_CONF); 

http_param(array('t' => 0) ); 

$olsa = new ls_article($ARTICLE_CONF); 
$olsa->mut_type($t); 

$lsa = $olsa->req(); 

include HAUT_ADMIN;
?>

<h1><?php echo $ARTICLE_CONF[$t]['nom'] ?></h1>

<?php if( article_verif_droit(article_droit_ajt($ARTICLE_CONF, $t), $MEMBRE->droit) ) : ?>
	<p><a href="article-form.php?t=<?php echo $t ?>" >Nouveau</a></p>
<?php endif ?>

<?php article_affiche($ARTICLE_CONF, $lsa) ?>

<?php include BAS_ADMIN ?>
