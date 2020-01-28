<?php
include '../../include/init.php'; 
include C_INC.'reqa_class.php'; 
include C_INC.'ls_location_class.php'; 
include C_INC.'location_class.php'; 

if( !droit(GERER_UTILISATEUR ) )
	senva(); 

/*
	Traitement 
*/

if( isset($_POST['ok']) )
{
	http_param( array('str' => array(), 'idext' => array(), 'ids' => array() ) ); 

	if( !empty($ids) )
	{
		location::sup($ids); 
		mess('Location supprimé !'); 
	}

	$pre = prereq('UPDATE Externe SET structure=? WHERE id=? LIMIT 1 '); 

	foreach( $idext as $i => $ide )
	{
		if( $str[ $i ] == -1 )
		{
			exereq($pre, array(0, $ide ) ); 
		}
		elseif( !empty($str[ $i ] ) )
		{
			exereq($pre, array($str[ $i ], $ide ) ); 
		}
	}
}

/*
	Affichage 
*/
http_param(array('p' => 0 ) ); 

$location = new ls_location; 
$location->page = $p; 
$location->ch_nom_str = TRUE; 
$loc = $location->requete(); 

$pagin = new pagin; 
$pagin->mut_nbp($loc->nb_page); 
$pagin->mut_url('location-admin.php?p=%p '); 
$pagin->mut_actif($p); 

$str = new reqa('SELECT secuhtml::nom, absint::id FROM structure WHERE actif=1 ORDER BY nom '); 
$option=''; 
while( $s = $str->parcours() ) 
{
	$option .='<option value="'.$s->id.'" >'.$s->nom.'</option>'; 
}

include HAUT_ADMIN;
?>

<h1>Liste des relais</h1>

<?php pmess() ?>

<p>Vous pouvez modifier la structure de chaque relais et faire plusieurs suppressions d'un coup.<br />
Séléctionnez les options appropriées et appuyez sur le bouton ok en dessous du tableau.<br />
Le lien "modifier" mène vers le formulaire de modification de la location.<br />
Options pour les structures : 
</p>

<ul>
	<li>Option par défaut : pas de changement.</li>
	<li>Option "Sans structure" : retire la structure.</li>
	<li>Sinon : assigne la location à la structure séléctionné.</li>
</ul>

<form action="location-admin.php" method="post" >

<?php $pagin->affiche() ?>

<table class="table_defaut" >
	<tr>
		<th>Code</th>
		<th>Nom</th>
		<th>Style</th>
		<th>Nom structure</th>
		<th>Nb rss</th>
		<th>Nb ext</th>
		<th>Page(s) appelantes</th>
		<th>Sélection de la structure</th>
		<th>Suppression</th>
		<th></th>
		<th></th>
		<th></th>
	</tr>
<?php while( $l = $loc->parcours() ) : ?>
	<tr>
		<td><?php echo $l->code ?></td>
		<td><?php echo $l->nom ?></td>
		<td><?php echo $l->template ?></td>
		<td><?php echo $l->structure ?></td>
		<td><?php echo $l->nb_rss ?></td>
		<td><?php echo $l->nb_ext ?></td>
		<td>
		<?php foreach($l->page_appelante as $pa ) : ?>
			<a href="<?php echo $pa ?>" ><?php echo $pa ?></a><br />
		<?php endforeach ?>
		<td>
			<select name="str[<?php echo $loc->num ?>]" >
				<option value="" >Choisir une Structure</option>
				<option value="-1" >Sans structure</option>
				<?php echo $option ?>	
			</select>
			<input type="hidden" name="idext[<?php echo $loc->num ?>]" value="<?php echo $l->id ?>" />
		</td>
		<td><input type="checkbox" name="ids[]" value="<?php echo $l->id ?>" /></td>
		<td><a href="location-form.php?idl=<?php echo $l->id ?>&amp;p=<?php echo $p ?>&amp;page=1" >Modifier</a></td>
		<td><a href="location-voir.php?p=<?php echo $p ?>&amp;page=1&amp;code=<?php echo $l->code ?>" >Voir</a></td>
		<td><a href="<?php echo ADD_SITE ?>externe/<?php echo $l->code ?>/0_0_FR.rss" >RSS</a></td>
	</tr>
<?php endwhile ?>
</table>
<p><input type="hidden" name="p" value="<?php echo $p ?>" />
<input type="submit" name="ok" value="Ok !" />
</p>

</form>

<?php include BAS_ADMIN ?>
