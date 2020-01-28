<?php

include '../../include/init.php'; 
include C_INC.'reqa_class.php'; 
include '../stat/include/stat_fonc.php'; 

$var= '';

if(isset($_POST['annee']) )
{
	$lancer=TRUE;
	$annee = isset($_POST['annee']) ? (int)$_POST['annee'] : (int)date('Y') ; 
	$moi = ''; 

	if($annee == date('Y') )
	{
		$lancer = FALSE;
		mess('On ne peut pas supprimé les événements de l\'année en cours. ');
	}

	$chemin = C_DOS_PHP.'archives/';
	if( !file_exists($chemin) )
	{
		if(!mkdir($chemin) )
		{
			$lancer = FALSE;
			mess('Pas de dossier pour la sauvegarde. ');
		}
	}

	$fichier = 'archive-'.$annee.'.xml';

	if(file_exists($chemin.$fichier) )
	{
		$lancer = FALSE;
		mess(' L\'archive à déjà été crée ! '); 
	}

	if($lancer )
	{

		/*
			Création du fichier de sauvegarde des statistiques. 
		*/

		/* Lei : */
		$stat_lei = stat_lei($annee, $moi ); 

		/* Nombre d'événement total de l'année */ 
		$evenement_total = nombre_evenement($annee, $moi );

		/* Nombre d'événement par département */ 
		$departement = stat_dep($annee, $moi ); 

		/* Nombre d'événement par contact */
		$contact = stat_contact($annee, $moi );  

		/* Nombre d'événements par categorie */
		$categorie = stat_categorie($annee, $moi ); 

		/* Nombre d'événements par theme */
		$theme = stat_theme($annee, $moi );

		/* Nombre d'événements mis en actif par modérateur */
		$moderateur = stat_moderateur($annee, $moi );

		ob_start(); 
		include '../stat/patron/stat_xml.p.php'; 
		$var = ob_get_contents(); 
		ob_end_clean(); 

		file_put_contents($chemin.$fichier, $var );

		/*
			Suppression des événements 
			#On bourrine un peu... 
		*/

		$donne = req("
			SELECT DISTINCT(Evenement_id) AS id
			FROM Evenement_dates 
			WHERE Evenement_date BETWEEN '$annee-01-01' AND '$annee-12-31'
		");

		$tab = []; 
		while( $do = fetch($donne) )
		{
			$tab[] = $do['id']; 
		}

		$donne = req("
			SELECT DISTINCT(Evenement_id) AS id
			FROM Evenement_dates 
			WHERE Evenement_date > '$annee-12-31' 
		");

		while($do = fetch($donne) )
		{
			if( ($k=array_search($do['id'], $tab) ) !== FALSE )
			{
				unset($tab[$k]); 
			}
		}

		$tf = array_chunk($tab, 100);

		foreach($tf as $t )
		{
			$donne = req('DELETE FROM Evenement WHERE id IN('.implode(',',$t).') '); 
		}
	}
}

include HAUT_ADMIN ; 
?>

<h1>Nettoyage</h1>

<?php pmess() ?>

<form action="net.php" method="post" >
<p>Séléctionné l'année à archivé : 
<select name="annee" >
	<?php for( $i=2005 ; $i<(int)date('Y') ; $i++ ) : ?>
		<option value="<?php echo $i ?>" ><?php echo $i ?></option>
	<?php endfor ?>
</select>
<input type="submit" name="ok" value="Ok !" />
</p>

</form>

<?php if(!empty($var ) ) : ?>
	<p>Fichier généré : </p> 
	<pre><?php echo secuhtml($var)?></pre>
<?php endif ?>

<?php include BAS_ADMIN ?>
