<?php
require_once '../../../include/init.php'; 
require_once C_INC.'structure_facture_class.php'; 

if( !isset($_GET['id']) )
{
	exit('fail - no id'); 
}

$pre = exepre('SELECT * FROM structure WHERE id=?', [ $_GET['id'] ]); 
$do = fetch($pre); 

if( !$do )
{
	exit('fail - no str'); 
}

$lsf = new reqo;
$lsf->mut_sorti('structure_facture'); 
$lsf->requete('
	SELECT structure structure__id, id, somme, date, type, dossier, fichier 
	FROM structure_facture 
	WHERE structure='.(int)$_GET['id'].'
	ORDER BY date DESC
'); 

?>
<div class="bl-infos" >
	<p><a class="ouvre-facture-form" href="#" >Ajouter une facture</a></p>

	<form class="ajt-facture-form" >
		<a href="#" class="date-facture-bt" ><span class="aff-date" ></span></a>
		Somme : <input type="text" name="somme" value="" size="4" />
		Type : <select name="type" >
		<?php foreach(structure_facture::$tab_type as $type => $nom ) : ?>
			<option value="<?php echo $type ?>" ><?php echo $nom ?></option>
		<?php endforeach ?>
		</select>
		<input class="ch-fichier" type="file" name="file" value="" />
		<input class="ajt-facture" type="submit" name="ok" value="Valider" />
		<input type="hidden" name="idf" value="" />
		<input type="hidden" name="date" value="" />
		<input class="facture-annule" type="button" name="no" value="Annuler" />
	</form>
	<table class="ls-facture" >
		<thead>
			<tr>
				<th>Date</th>
				<th>Somme</th>
				<th>Type</th>
				<th>Fichier</th>
			</tr>
		<thead>
		<tbody>
		<?php while($f = $lsf->parcours() ) :  ?>
			<?php $f->aff_ligne() ?>
		<?php endwhile ?>
		</tbody>
	</table>
</div>
