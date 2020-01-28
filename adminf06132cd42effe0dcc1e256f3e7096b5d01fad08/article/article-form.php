<?php
include '../../include/init.php'; 
include C_INC.'reqa_class.php'; 
include C_INC.'article_class.php'; 

http_param(array('t'=>0, 'ida' => 0 ) ); 

$a = new gere_article($ARTICLE_CONF);
$succes=FALSE; 

if( $MEMBRE->connecte )
{

	if(isset($_POST['ok']) )
	{
		http_param(array('etat' => 0, 'titre' => '', 'article' => '', 'id' => 0, 'com' => '' ) ); 
		$a->mut_type($t); 
		$a->mut_etat($etat, $MEMBRE->droit ); 
		$a->titre = $titre; 
		$a->article = $article;
		$a->commentaire = !empty($com); 
		$a->id = $id; 
		$a->mut_id_createur($MEMBRE->id); 
		if($a->enr($MEMBRE->droit, $MEMBRE->id) )
		{
			$succes=TRUE; 
		}
		else
		{
			mess('Erreur.'); 
		}
	}
	elseif(!empty($ida) )
	{
		$a->bdd($ida); 	
	}
	else
	{
		$a->mut_type($t); 
	}
}

include HAUT_ADMIN;
?>

<h1><?php echo $a->acc_type() ?></h1>

<?php pmess() ?>

<p><a href="liste-article.php?t=<?php echo $t ?>" >Retour</a></p>

<?php if(!$succes && $MEMBRE->connecte) : ?>

	<form action="article-form.php" method="post" >
	<?php if(!empty($ARTICLE_CONF[$a->acc_type(TRUE)]['etat']) ) : ?>
	<p><select name="etat" >
		<?php foreach($ARTICLE_CONF[$a->acc_type(TRUE)]['etat'] as $ide => $etat ) : ?>
			<option value="<?php echo $ide ?>" 
			<?php if( !article_verif_droit(article_droit_etat($ARTICLE_CONF, $a->acc_type(TRUE), $ide), $MEMBRE->droit) ) : ?>
			disabled="disabled"
			<?php endif ?>
			<?php if($a->acc_etat(TRUE) == $ide) : ?>selected="selected"<?php endif ?>><?php echo $etat ?></option>
		<?php endforeach ?>
	</select>
	</p>
	<?php endif ?>

	<p>Titre : <input type="text" name="titre" value="<?php echo $a->titre ?>" size="30" /></p>

	<p>Article : <br /><textarea name="article" rows="7" cols="70" ><?php echo $a->article ?></textarea></p>

	<p>
	  <label>Ouvert au commentaire : 
	    <input type="checkbox" name="com" <?php if($a->commentaire) : ?>checked="checked"<?php endif ?> /></label></p>

	<p>
	<input type="hidden" name="t" value="<?php echo $a->acc_type($num=TRUE) ?>" />
	<input type="hidden" name="id" value="<?php echo $a->id ?>" />
	<input type="submit" name="ok" value="Ok !" />
	</p>
	</form>
<?php elseif($succes ) : ?>

	<p>La demande a correctement été prise en compte.</p>
<?php else : ?>
	<p>Seul les membres peuvent ajouter un article.</p>
<?php endif ?>


<?php include BAS_ADMIN ?>
