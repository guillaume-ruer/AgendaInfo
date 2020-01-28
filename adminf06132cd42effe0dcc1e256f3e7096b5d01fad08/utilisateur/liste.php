<?php
include '../../include/init.php';
include C_INC.'membre_fonc.php'; 

include C_INC.'reqa_class.php'; 

ajt_style('style-droit.css', C_ADMIN.'style/' ); 

http_param(array('p' => 0, 'u' => 0, 'rech' => '') ); 

if(!empty($u) )
{
	utilisateur_sup($u); 	
}

$where = ''; 

if( !empty($rech) )
{
	$like = 'LIKE(\'%'.secubdd($rech).'%\')';
	$where = " WHERE u.User $like OR u.prenom $like OR u.nom $like ";
}

$utilisateur = new reqa('
	SELECT absint::u.ID id, secuhtml::User pseudo, absint::droit, secuhtml::prenom, secuhtml::u.nom, 
		secuhtml::str.nom structure 
	FROM Utilisateurs u
	LEFT OUTER JOIN structure str 
		ON str.id = u.id_structure 
	'.$where.' 
	ORDER BY User 
', array(), $p, 50 );

$url_rech = urlencode($rech); 

include HAUT_ADMIN;
?>

<h1>Liste des utilisateurs</h1>

<form action="liste.php" method="post" >
	<p>Recherche (login, prénom, nom) : <input type="text" name="rech" value="<?php echo $rech ?>" />
		<input type="submit" name="ok" value="Ok !" />
	</p>
</form>

<?php if($utilisateur->nb_page > 0 ) : ?>
	<p>
	<?php for($i=0; $i<$utilisateur->nb_page; $i++ ) : ?>
	<a href="liste.php?p=<?php echo $i ?>&amp;rech=<?php echo $url_rech ?>" <?php echo ($i == $p ) ? 'class="actif"' : '' ?> ><?php echo $i+1 ?></a>
	<?php endfor ?>
	</p>
<?php endif ?>

<div id="bulle" >Text</div>

<p>Une infobulle apparaît pour décrire le droit correspondant à la case pointé.</p>

<table class="table_defaut" id="tab" >
	<caption>Utilisateurs</caption>
	<tr>
		<th rowspan="2" > Login </th>
		<th rowspan="2" > Prénom </th>
		<th rowspan="2" > Nom </th>
		<th rowspan="2" > Structure </th>
		<th colspan="<?php echo NB_DROIT ?>" >
			Droits
		</th>
		<th rowspan="2" colspan="2" >Action</th>
	</tr>
	<tr id="entete" >
		<?php foreach($TAB_DROIT as $droit ) : ?>
			<th><span title="<?php echo $droit['desc'] ?>" ><?php echo $droit['petit'] ?></span></th>
		<?php endforeach ?>
	</tr>
<?php while( $u = $utilisateur->parcours() ) : ?>
	<tr class="<?php echo $utilisateur->switch ? 'ligne1' : 'ligne2' ?> ligne" >
		<td><?php echo $u->pseudo ?></td>
		<td><?php echo $u->prenom ?></td>
		<td><?php echo $u->nom ?></td>
		<td><?php echo $u->structure ?></td>

		<?php foreach($TAB_DROIT as $droit ) : ?>
			<?php if($u->droit & $droit['bit'] ) : ?>
				<td class="droit_oui" >Oui</td>
			<?php else : ?>
				<td class="droit_non" >Non</td>
			<?php endif ?>
		<?php endforeach ?>
		<td><a href="utilisateur-form.php?u=<?php echo $u->id ?>&amp;p=<?php echo $p ?>&amp;rech=<?php echo $url_rech ?>" >Modifier</a></td>
		<td><a href="liste.php?u=<?php echo $u->id ?>&amp;p=<?php echo $p ?>&amp;rech=<?php echo $url_rech ?>" 
			onclick="return confirm('Vouez vous vraiment supprimer cette utilisateur ?')" >Supprimer</a></td>
	</tr>
<?php endwhile ?>
</table>

<script type="text/javascript" >

$(function(){
	var div = $('#bulle').css({ 
		position:"absolute",
		background:"white",
		border:"1px solid black", 
		padding:"5px"
	}); 

	div.hide(); 

	$('.ligne td').mousemove( function(e){
		var ind = $(this).index(); 
		if( ind > 3 && ind < <?php echo count($TAB_DROIT)+4 ?>)
		{
			div.html( $('#entete th:eq('+(ind-4)+') span').attr('title') ); 
			div.show(); 
			div.css({
				top:e.pageY-30,
				left:e.pageX+30
			}); 
		}
	}); 

	$('.ligne td').mouseleave( function(e){
		div.hide(); 
	}); 
}); 

</script>

<?php include BAS_ADMIN ?>
