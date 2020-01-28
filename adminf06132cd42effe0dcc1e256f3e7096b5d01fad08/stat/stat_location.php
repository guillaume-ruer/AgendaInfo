<?php
include '../../include/init.php'; 

if(!droit(TOUT_STAT ) )
{
	include PAT_ERREUR;
	exit(); 
}

include C_INC.'reqa_class.php'; 

$id = isset($_POST['loca']) ? absint($_POST['loca']) : 0;
$annee = isset($_POST['annee']) ? (int)$_POST['annee'] : (int)date('Y') ; 
$moi = isset($_POST['moi']) ? (int)$_POST['moi'] : ''; 

if(!empty($id) )
{
	$where = fi_date($annee, $moi);
	$join = '';

	$tab = array(); 
	$donne = req('SELECT id_contact FROM Externe_contact WHERE id_externe='.$id );
	while($do = fetch($donne) )
	{
		$tab_contact[] = $do['id_contact'];
	}

	if(!empty($tab_contact) )
	{
		$ls_contact = implode(',',$tab_contact);
		$where .= "AND Contact_id IN(".$ls_contact.")\n"; 
	}

	$tab = array(); 
	$donne = req('SELECT id_lieu FROM Externe_lieux WHERE id_externe='.$id );
	while($do = fetch($donne) )
	{
		$tab_lieu[] = $do['id_lieu'];
	}

	if(!empty($tab_lieu) )
	{
		$ls_lieu =implode(',',$tab_lieu);
		$where .= "AND el.Lieu_id IN(".$ls_lieu.")\n"; 
		$join = "LEFT JOIN Evenement_lieux AS el\n\tON el.Evenement_id = e.id\n";
	}

	$stat = req('
		SELECT COUNT(DISTINCT e.id) AS nbe 
		FROM Evenement AS e
		LEFT JOIN Evenement_dates 
			ON Evenement_dates.Evenement_id = e.id
		'.$join.'
		WHERE  
		'.$where.'
	');
	$do = fetch($stat); 
	$nb_event = (int)$do['nbe'];

	
	if(!empty($tab_lieu) )
	{
		$lieu = new reqa('
			SELECT secuhtml::Lieu_Ville
			FROM Lieu 
			WHERE Lieu_ID IN( '.$ls_lieu.')
		');
	}

	if(!empty($tab_contact) )
	{
		$contact = new reqa('
			SELECT secuhtml::adherent 
			FROM Contact 
			WHERE id IN('.$ls_contact.')
		');
	}

}

/* Filtrage par location */

$location = new reqa('
	SELECT absint::id, secuhtml::nom 
	FROM Externe 
	WHERE nom!=\'\'
	ORDER BY nom 
'); 


include HAUT_ADMIN; 
?>

<h1 id="haut" >Statistiques de <?php echo (!empty($moi)  ? $tab_mois[ $moi-1 ] : "toute l'année").' '.$annee ?></h1>

<form action="stat_location.php" method="post" >
	<p>Année : <select name="annee" >
		<?php for($i = 2005; $i <= date('Y'); $i++ ) : ?>
			<option value="<?php echo $i ?>" <?php echo $annee == $i ? 'selected="selected"' : '' ?> ><?php echo $i ?></option>
		<?php endfor ?>
	</select>
	Mois : <select name="moi" >
		<option value="" >Tout</option>
		<?php for($i=1; $i<= 12; $i ++) : ?>
			<option value="<?php echo $i ?>" <?php echo $moi == $i ? 'selected="selected"' : '' ?> ><?php echo $tab_mois[ $i-1 ] ?></option>
		<?php endfor ?>
	</select>
	</p>

	<p>Location : <select name="loca" >
		<option value="" >Choisir une location</option>
		<?php while($lo = $location->parcours() ) : ?>
			<option value="<?php echo $lo->id ?>" <?php echo $lo->id == $id ? 'selected="selected"' : '' ?> ><?php echo $lo->nom ?></option>
		<?php endwhile ?>
	</select>
	</p>

	<p><input type="submit" name="ok" value="Ok !" /></p>
</form>

<?php if(empty($id ) ) : ?>
	
	<p>Aucune location séléctionné.</p>
<?php else : ?>

	<p>Nombre d'événements : <?php echo $nb_event ?></p>

	<h3>Lieu </h3>
	<?php if(empty($tab_lieu ) ) : ?>
		<p>Tout les lieu.</p>
	<?php else : ?>
		<ul>
		<?php while($l = $lieu->parcours() ) : ?>
			<li><?php echo $l->Lieu_Ville ?></li>
		<?php endwhile ?>
		</ul>
	<?php endif ?>

	<h3>Contact</h3>
	<?php if(empty($tab_contact) ) : ?>
		<p>Tout les contact.</p>
	<?php else : ?>
		<ul>
		<?php while($c = $contact->parcours() ) : ?>
			<li><?php echo $c->adherent ?></li>
		<?php endwhile ?>
		</ul>
	<?php endif ?>

<?php endif ?>

<?php include BAS_ADMIN ?>

