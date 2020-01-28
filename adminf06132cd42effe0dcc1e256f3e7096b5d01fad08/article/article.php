<?php

include '../../include/init.php'; 
include C_INC.'reqa_class.php'; 
include C_INC.'article_class.php'; 

http_param(array('id' => 0, 'p'=>0 ) );

$a = new gere_article($ARTICLE_CONF); 

if(isset($_POST['ok']) AND $MEMBRE->connecte )
{
	http_param(array('com' => '' ) ); 
	$c = new gere_commentaire; 
	$c->id_article = $id; 
	$c->commentaire = $com; 
	$c->id_utilisateur = $MEMBRE->id; 
	$c->insert(); 
}
elseif( isset($_GET['sc']) )
{
	$a->sup_commentaire($_GET['sc'], $MEMBRE); 
}

// Configure les commentaire 
$a->c_page = $p; 
$a->c_nbparpage = 10; 

if(!$a->bdd($id) )
{
	mess('Erreur, l\'identifiant ne correspond à aucun article.'); 
}

include HAUT_ADMIN; 
?>

<?php pmess() ?>

<p><a href="liste-article.php?t=<?php echo $a->acc_type(TRUE) ?>" >Retour</a></p>

<h3><?php echo $a->titre ?></h3>

<p><?php echo $a->article ?></p>

<p class="auteur" >Par <?php echo $a->acc_createur() ?> le <?php echo $a->acc_date() ?></p>

<h3>Commentaires</h3>

<?php if($a->c_nbpage > 1 ) : ?>
<p>
<?php for($i=0; $i<$a->c_nbpage; $i++ ) : ?>
	<a href="article.php?id=<?php echo $id ?>&amp;p=<?php echo $i ?>" ><?php echo $i+1 ?></a>
<?php endfor ?>
</p>
<?php endif ?>

<?php for($i=0; $com = $a->acc_commentaire($i); $i++ ) : ?>

	<p><?php echo $com->commentaire ?></p>
	<p>Par <?php echo $com->utilisateur ?> le <?php echo $com->acc_date() ?>

	<?php if(article_com_droit($ARTICLE_CONF, $com->id_utilisateur, $MEMBRE, $a->acc_type(TRUE) ) ) : ?>
		[<a href="article.php?id=<?php echo $a->id ?>&amp;sc=<?php echo $com->id ?>&amp;p=<?php echo $p ?>" >Supprimer</a>]
	<?php endif ?>
	</p>

<?php endfor ?>

<?php if($MEMBRE->connecte) : ?>
<form action="article.php" method="post" >

<p><textarea name="com" rows="7" cols="70" ></textarea></p>
<p><input type="hidden" name="id" value="<?php echo $a->id ?>" /></p>
<p><input type="submit" name="ok" value="Ok !" /></p>

</form>

<?php else : ?>

<p>Seul les membres peuvent commenter.</p>

<?php endif ?>

<?php include BAS_ADMIN ?>
