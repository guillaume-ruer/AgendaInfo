<?php
include '../../include/init.php'; 
include C_INC.'reqa_class.php'; 

http_param(array(
	'nom_lei' => '',
	'id_lei' => '',
	'infolimo' => 0
) ); 

/*
	Traitement 
*/

$succe = FALSE; 
if(isset($_POST['ok'], $_POST['infolimo'], $_POST['id_lei'], $_POST['nom_lei']) )
{
	$bon = TRUE; 
	$nom_lei = trim($nom_lei); 
	$infolimo = trim($infolimo); 
	
	if(empty($nom_lei) OR empty($infolimo) )
	{
		mess('L\'un des champs est vide.'); 
		$bon = FALSE; 
	}

	if($bon )
	{
		$donne = req('SELECT id_lei FROM contact_lei WHERE id_lei=\''.secubdd($id_lei).'\' LIMIT 1  ');	

		if($do = fetch($donne) )
		{
			//Modif 		
			req('UPDATE contact_lei SET id_infolimo='.$infolimo.', nom_lei=\''.secubdd($nom_lei).'\' WHERE id_lei=\''.secubdd($id_lei).'\' LIMIT 1 '); 
			mess('Correspondance mise à jour. '); 
		}
		else
		{
			//Ajout 

			req('INSERT INTO contact_lei(id_lei, id_infolimo, nom_lei, source) VALUES(\''.secubdd($id_lei).'\', '.$infolimo.', \''.secubdd($nom_lei).'\', '.$SOURCE.' ) '); 
			mess('Nouvelle correspondance ajoutée. '); 
		}

		$succe = TRUE; 
	}
}

/*
	HTML 
*/

$modif = FALSE;

//Remplissage automatique des champs dans le cas d'une modif 
if(isset($_GET['id']) )
{
	$contact_mod = req('SELECT id_lei, nom_lei, id_infolimo FROM contact_lei WHERE id_lei='.(int)$_GET['id']. ' LIMIT 1 ');

	if($do = fetch($contact_mod) )
	{
		$nom_lei = secuhtml($do['nom_lei']);
		$id_lei = secuhtml($do['id_lei']);
		$infolimo = (int)$do['id_infolimo']; 
		$modif = TRUE;
	}
	else
	{
		mess('Aucun contact n\'a été trouvé'); 
	}
}

$contact= new reqa('
	SELECT absint::sc.id, secuhtml::sc.titre, secuhtml::s.nom
	FROM structure_contact sc
	LEFT JOIN structure s
		ON s.id = sc.id_structure
	ORDER BY TRIM(s.nom)
'); 

include HAUT_ADMIN;
?>
<h3>Ajout d'une correspondance de compte </h3>

<p><a href="contact-lei.php" >Retour</a></p>

<?php pmess() ?>

<?php if(!$succe) : ?>
	<form action="contact-form.php" method="post" >
		<p>Nom <?php evenement::aff_source_nom($SOURCE) ?> : <input type="text" name="nom_lei" value="<?php echo $nom_lei; ?>" size="50" /> 
			<?php if($modif ) : ?>
				Id : <?php echo $id_lei ?> <input type="hidden" name="id_lei" value="<?php echo $id_lei ?>" />
			<?php else : ?>
				Id : <input type="text" name="id_lei" value="<?php echo $id_lei; ?>" size="5" />
			<?php endif ?>

		</p>
		
		<p>Contact info-limousin : <select name="infolimo" >
		<?php while($co = $contact->parcours() ) : ?>
			<option value="<?php echo $co->id; ?>" <?php echo ($infolimo == $co->id) ? 'selected="selected"' : ''; ?> >
				<?php echo $co->nom ?>
				<?php if(!empty($co->titre) ) : ?>
					<?php echo ', ', $co->titre ?>
				<?php endif ?>
			</option>
		<?php endwhile ?>
		</select>
		</p>

		<p><input type="submit" name="ok" value="Envoi !" /></p>
	</form>

<?php endif ?>

<?php include BAS_ADMIN; ?>
