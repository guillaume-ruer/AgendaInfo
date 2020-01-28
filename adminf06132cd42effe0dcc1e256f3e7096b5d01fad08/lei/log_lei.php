<?php
include '../../include/init.php';
include C_INC.'reqa_class.php'; 

$p = (isset($_GET['p']) ) ? absint($_GET['p']) : 0; 

$log_lei = new reqa('SELECT absint::actif, absint::supprime, absint::masque, madate::time,
	absint::nbverif, absint::nbins, absint::nbmaj, absint::nbelim, absint::nbsup,
	absint::nbreq, absint::nbpre, absint::nbexe, secuhtml::tps, absint::auto_actif nbact
	FROM stat_lei 
	WHERE source='.$SOURCE.'
	ORDER BY time DESC ', NULL, $p, 30 );

$pagin = new pagin_reqo; 
$pagin->mut_num_page($p); 
$pagin->mut_url('log_lei.php?p=%pg'); 
$pagin->mut_mode(pagin_reqo::COUPE); 
$pagin->mut_nb_page($log_lei->nb_page); 

include HAUT_ADMIN;
?>

<?php $pagin->affiche() ?>

<table class="table_defaut" >
	<caption>Log de l'importation du <?php evenement::aff_source_nom($SOURCE) ?></caption> 
	<tr>
		<th>Date</th>
		<th>Actif</th>
		<th>Masqué</th>
		<th>Supprimé</th>
		<th>Nb événement</th>
		<th>Nb insertion</th>
		<th>Nb mise à jour</th>
		<th>Nb mise en actif automatiquement</th>
		<th>Nb événement éliminé</th>
		<th>Nb événement supprimé</th>
		<th>Nb requêtes</th>
		<th>Nb requêtes préparés </th>
		<th>Nb requêtes préparés exectué </th>
		<th>Temps de boucle</th>
	</tr>
	<?php while( $li = $log_lei->parcours() ) : ?>
		<tr>
			<td><?php echo $li->time ?></td>
			<td><?php echo $li->actif ?></td>
			<td><?php echo $li->masque ?></td>
			<td><?php echo $li->supprime ?></td>
			<td><?php echo $li->nbverif ?></td>
			<td><?php echo $li->nbins ?></td>
			<td><?php echo $li->nbmaj ?></td>
			<td><?php echo $li->nbact ?></td>
			<td><?php echo $li->nbelim ?></td>
			<td><?php echo $li->nbsup ?></td>
			<td><?php echo $li->nbreq ?></td>
			<td><?php echo $li->nbpre ?></td>
			<td><?php echo $li->nbexe ?></td>
			<td><?php echo $li->tps ?></td>
		</tr>
	<?php endwhile ?>
</table>

<?php include BAS_ADMIN ?>
