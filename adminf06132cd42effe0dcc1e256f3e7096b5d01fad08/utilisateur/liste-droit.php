<?php
include '../../include/init.php';

include C_INC.'reqa_class.php'; 

ajt_style('style-droit.css', C_ADMIN.'style/' ); 

http_param(array('p'=>0, 'rech' => '' ) ); 

if( !empty($rech) )
{
	$where = ' WHERE User LIKE  (\'%'.secubdd($rech).'%\')';
}
else
{
	$where = ''; 
}

$utilisateur = new reqa('
	SELECT absint::u.ID id, secuhtml::User pseudo, absint::droit
	FROM Utilisateurs u
	'.$where.' 
	ORDER BY User 
', array(), $p, 50 );

include HAUT_ADMIN;
?>
<h1>Utilisateurs et droits</h1>

<form action="liste-droit.php" method="post" >
	<p>Recherche (login) : <input type="text" name="rech" value="<?php echo $rech ?>" />
		<input type="submit" name="ok" value="Ok !" />
	</p>
</form>

<?php if($utilisateur->nb_page > 0 ) : ?>
	<p>
	<?php for($i=0; $i<$utilisateur->nb_page; $i++ ) : ?>
	<a href="liste-droit.php?p=<?php echo $i ?>&amp;rech=<?php echo $rech ?>" <?php echo ($i == $p ) ? 'class="actif"' : '' ?> ><?php echo $i+1 ?></a>
	<?php endfor ?>
	</p>
<?php endif ?>

<table class="table_defaut" >
	<caption>Utilisateurs</caption>
	<tr>
		<th rowspan="2" >
			Login
		</th>
		<th colspan="<?php echo NB_DROIT ?>" >
			Droits
		</th>
		<th rowspan="2" >Action</th>
	</tr>
	<tr>
		<?php foreach($TAB_DROIT as $droit ) : ?>
			<th><?php echo $droit['nom'] ?></th>
		<?php endforeach ?>
	</tr>
<?php while( $u = $utilisateur->parcours() ) : ?>
	<tr class="<?php echo $utilisateur->switch ? 'ligne1' : 'ligne2' ?>" >
		<td><?php echo $u->pseudo ?></td>

		<?php foreach($TAB_DROIT as $droit ) : ?>
			<?php if($u->droit & $droit['bit'] ) : ?>
				<td class="droit_oui" >Oui</td>
			<?php else : ?>
				<td class="droit_non" >Non</td>
			<?php endif ?>
		<?php endforeach ?>
		<td><a href="utilisateur-form.php?u=<?php echo $u->id ?>&amp;p=<?php echo $p ?>" >Modifier</a></td>
	</tr>
<?php endwhile ?>
</table>

<?php include BAS_ADMIN ?>
