
<h1 id="haut" >Statistiques de <?php echo (!empty($moi)  ? $tab_mois[ $moi-1 ] : "toute l'année").' '.$annee ?></h1>

<form action="stat.php" method="post" >

	<p>Année : <select name="annee" >
		<?php for($i = 2005; $i <= date('Y'); $i++ ) : ?>
			<option value="<?php echo $i ?>" <?php echo $annee == $i ? 'selected="selected"' : '' ?> ><?php echo $i ?></option>
		<?php endfor ?>
	</select>
	Mois : <select name="moi" >
		<option value="0" >Tout</option>
		<?php for($i=1; $i<= 12; $i ++) : ?>
			<option value="<?php echo $i ?>" <?php echo $moi == $i ? 'selected="selected"' : '' ?> ><?php echo $tab_mois[ $i-1 ] ?></option>
		<?php endfor ?>
	</select>
	<input type="submit" name="ok" value="Ok !" /></p>

</form>

<ul>
	<li><a href="#departement" >Département</a></li>
	<li><a href="#adherent" >Adhérent</a></li>
	<li><a href="#categorie" >Catégories</a></li>
	<li><a href="#theme" >Thème</a></li>
	<li><a href="#moderateur" >Modérateur</a></li>
</ul>

<p>Nombre d'évenement : <?php echo $evenement_total ?></p>

<div class="table_defaut" >

<?php if( $lei  = $stat_lei->parcours() ) : ?>
	<table>
		<caption>Ev&eacute;nements provenant du LEI</caption>
	<?php do {  ?>
		<tr>
			<th><?php echo $lei->nom; ?></th>
			<td><?php echo $lei->nb; ?></td>
		</tr>
	<?php } while($lei = $stat_lei->parcours() ) ?>
	</table>
<?php endif ?>

<p><a href="#haut" >Retour en haut</a></p>

<table id="departement" >
	<caption>Par département</caption>
	<tr>
		<th>Département</th>
		<th>Nombre</th>
	</tr>
<?php $totale=0; while($do = $departement->parcours() ) : $totale += $do->nbe ?>
	<tr>
		<th><?php echo $do->dep ?></th>
		<td><?php echo $do->nbe ?></td>
	</tr>
<?php endwhile ?>
	<tr>
		<th>Totale </th>
		<td><?php echo $totale ?></td>
	</tr>
</table>
<p ><span class="petit" >(La différence du totale éventuellement constaté peut être dû au fait qu'un événement peut avoir plusieurs lieux.)</span></p>

<p><a href="#haut" >Retour en haut</a></p>

<table id="adherent" >
	<caption>Contact</caption>
	<tr>
		<th>Id</th>
		<th>Adhérent</th>
		<th>Nombre</th>
	</tr>
<?php $totale=0; while($co = $contact->parcours() ) : $totale+=$co->nbe ?>
	<tr>
		<td><?php echo $co->id ?></td>
		<td><?php echo empty($co->adh) ? '<span class="sans_nom" >Sans nom</span>' : $co->adh ?></td>
		<td><?php echo $co->nbe ?></td>
	</tr>
<?php endwhile ?>
	<tr>
		<th colspan="2" >Totale </th>
		<td><?php echo $totale ?></td>
	</tr>
</table>

<p><a href="#haut" >Retour en haut</a></p>

<table id="categorie" >
	<caption>Catégories</caption>
	<tr>
		<th>Id</th>
		<th>Catégorie</th>
		<th>Nombre</th>
	</tr>
<?php $totale=0; while($ca = $categorie->parcours() ) : $totale+=$ca->nbe ?>
	<tr>
		<td><?php echo $ca->id ?></td>
		<td><?php echo empty($ca->nom) ? '<span class="sans_nom" >Sans nom</span>' : $ca->nom ?></td>
		<td><?php echo $ca->nbe ?></td>
	</tr>
<?php endwhile ?>
	<tr>
		<th colspan="2" >Totale </th>
		<td><?php echo $totale ?></td>
	</tr>
</table>

<p><a href="#haut" >Retour en haut</a></p>

<table id="theme" >
	<caption>Thème</caption>
	<tr>
		<th>Id</th>
		<th>Thème</th>
		<th>Nombre</th>
	</tr>
<?php $totale=0; while($th = $theme->parcours() ) : $totale+=$th->nbe ?>
	<tr>
		<td><?php echo $th->id ?></td>
		<td><?php echo empty($th->nom) ? '<span class="sans_nom" >Sans nom</span>' : $th->nom ?></td>
		<td><?php echo $th->nbe ?></td>
	</tr>
<?php endwhile ?>
	<tr>
		<th colspan="2" >Totale </th>
		<td><?php echo $totale ?></td>
	</tr>
</table>

<p><a href="#haut" >Retour en haut</a></p>

<p>Le nombre de fois qu'un modérateur a mis un événement actif (compte plusieurs fois le même si il l'a mis plusieurs fois actif) </p>

<table id="moderateur" >
	<caption>Modérateur</caption>
	<tr>
		<th>Modérateur</th>
		<th>Nombre</th>
	</tr>
<?php $totale=0; while($mod = $moderateur->parcours() ) : $totale+=$mod->nb ?>
	<tr>
		<td><?php echo empty($mod->User) ? '<span class="sans_nom" >Sans nom</span>' : $mod->User ?></td>
		<td><?php echo $mod->nb ?></td>
	</tr>
<?php endwhile ?>
	<tr>
		<th>Totale </th>
		<td><?php echo $totale ?></td>
	</tr>

</table>

</div> 
