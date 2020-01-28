<?php
require '../include/init.php'; 
require C_INC.'fonc_cache.php'; 

$id = cache_id(); 

if( cache($id, 24 * 3600) ) {

$dt = req('SELECT nom_fr nom, id FROM categories_grp ORDER BY id'); 
$dst = req('SELECT CAT_NAME_FR nom, CAT_ID id FROM Categories ORDER BY id');

require HAUT; 
?>

<div > 

<h2 style="text-align:center" >Thèmes</h2>

<table style="margin:auto;">
	<tr>
		<th>Class</th>
		<th>Nom</th>
	</tr>
<?php while( $do = fetch($dt) ) : ?>
	<tr>
		<td>ila_theme_<?php echo $do['id'] ?>&nbsp;&nbsp;</td>
		<td><?php echo $do['nom'] ?></td>
	</tr>
<?php endwhile ?>
</table>

<hr />

<h2 style="text-align:center" >Sous-thèmes</h2>

<table style="margin:auto;">
	<tr>
		<th>Class</th>
		<th>Nom</th>
	</tr>
<?php while( $do = fetch($dst) ) : ?>
	<tr>
		<td>ila_sous_theme_<?php echo $do['id'] ?>&nbsp;&nbsp;</td>
		<td><?php echo $do['nom'] ?></td>
	</tr>
<?php endwhile ?>
</table>

</div>


<?php require BAS ?>

<?php } cache(); ?>
