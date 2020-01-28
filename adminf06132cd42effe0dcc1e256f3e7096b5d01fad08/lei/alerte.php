<?php
include '../../include/init.php'; 
include 'include/var-alerte.php'; 
require C_INC.'ls_alerte_class.php';
require C_INC.'alerte_class.php';
require C_INC.'alerte_fonc.php'; 

include C_INC.'reqa_class.php'; 

if(isset($_GET['id']) )
{
	if( alerte_verifier($_GET['id']) )
	{
		mess('Alerte retiré');
	}
}

http_param(array( 'p' => 0, 'type' => ALERTE_LEI_DATE ) );

debug($SOURCE); 

$alerte = new ls_alerte(array('type' => $type, 'sorti' => 'alerte', 'source' => $SOURCE) ); 
$alerte->acc_pagin()->mut_num_page($p); 
$alerte->acc_pagin()->mut_url('alerte.php?p=%pg&amp;type='.$type); 
$alerte->acc_pagin()->mut_mode(pagin_reqo::COUPE); 
$alerte->requete(); 

include HAUT_ADMIN ;
?>

<h1>Alerte événements</h1>

<?php pmess() ?>

<form action="alerte.php" method="post" >
	<p><select name="type" >
	<?php foreach($TYPE_ALERTE as $id => $nom ) : ?>
		<option value="<?php echo $id ?>" <?php echo $type == $id ? 'selected="selected"' : '' ?> ><?php echo $nom ?></option>
	<?php endforeach ?>
	</select>
	<input type="submit" name="ok" value="Ok !" /></p>
</form>

<?php $alerte->acc_pagin()->affiche() ?>

<table class="table_defaut" >
	<tr>
		<th>Editer</th>
		<th>Titre</th>
		<th>Type</th>
		<th>Cause</th>
		<th>Date</th>
		<th>Résolue</th>
	</tr>
<?php while($a = $alerte->parcours() ) : ?>
	<tr>
		<td><a href="<?php echo C_ADMIN; ?>evenement/event-form.php?id_maj=<?php echo $a->acc_idevent() ?>&amp;p=<?php echo $p ?>&amp;type=<?php echo $type ?>" >edit</a></td>
		<td><?php echo $a->acc_titre() ?></td>
		<td><?php echo $TYPE_ALERTE[ $a->acc_type() ] ?></td>
		<td><?php echo nl2br($a->acc_cause() ) ?></td>
		<td><?php echo madate($a->acc_time() ) ?></td>
		<td><a href="alerte.php?id=<?php echo $a->acc_id() ?>&amp;p=<?php echo $p ?>&amp;type=<?php echo $type ?>" >Résolu</a></td>
	</tr>
<?php endwhile ?>

</table>

<?php include BAS_ADMIN; ?>
