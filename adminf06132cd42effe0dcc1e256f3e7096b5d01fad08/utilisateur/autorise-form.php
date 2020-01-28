<?php 
include '../../include/init.php';
include C_INC.'reqa_class.php'; 
include C_ADMIN.'include/ls_contact_class.php'; 

http_param(array( 'co'=>0 , 'ids' => 0 ) );  

if(!empty($ids) )
{
	$donne = req('DELETE FROM autorise WHERE id_autorise = '.$ids.' AND idu='.ID.' LIMIT 1 '); 
}


if(!empty($co) )
{
	//co est l'id de contact 

	$id_utilisateur = req('SELECT ID FROM Utilisateurs WHERE Contact_id='.$co.' LIMIT 1 '); 
	$tab_idu = fetch($id_utilisateur); 
	$ida = absint($tab_idu['ID']); 

	if(empty($ida) )
	{
		mess("Aucun compte utilisateur pour ce contact.");
	}
	else
	{

		$donne = req('SELECT idu FROM autorise WHERE idu='.ID.' AND id_autorise='.$ida.' ');
		if( !fetch($donne) )
		{
			req('INSERT INTO autorise(idu, id_autorise)
				VALUES('.ID.', '.$ida.') ');
			mess('Utilisateur ajouté.'); 
		}
		else
		{
			mess('Utilisateur déjà dans la liste.');
		}
	}
}

$liste_contact = new liste_contact; 
$liste_contact->compte_lie=TRUE; 
$contact = $liste_contact->requete(); 


/*
	Liste de personne que l'utilisateur autorise
*/

$jautorise = new reqa('
        SELECT absint::u.ID id, secuhtml::c.adherent 
        FROM autorise a
	LEFT JOIN Utilisateurs u
		ON a.id_autorise=u.id
	LEFT JOIN Contact c
		ON c.id = u.Contact_id 
	WHERE a.idu='.ID.'
        ORDER BY User 
');

/*
	Liste de personne qui autorise l'utilisateur 
*/

$jsuisautorise = new reqa('
        SELECT secuhtml::c.adherent
        FROM autorise a
	LEFT JOIN Utilisateurs u
		ON a.idu=u.id
	LEFT JOIN Contact c
		ON c.id = u.Contact_id 
	WHERE a.id_autorise='.ID.'
        ORDER BY User 
');

include HAUT_ADMIN;
?>

<h1>Autorisations</h1>

<?php pmess() ?>

<form action="autorise-form.php" method="post" >

<p>Chercher un utilisateur (on de compte) : 
<select name="co" >
	<?php while($do = $contact->parcours() ) : ?>
	<option value="<?php echo $do->id ?>" ><?php echo $do->adherent ?> (<?php echo $do->ville ?>)</option>
	<?php endwhile ?>
</select>
<input type="submit" name="ok" value="Ok !" /></p>

</form>

<?php if($do = $jautorise->parcours() ) : ?>
	<table class="table_defaut" >
		<caption>Utilisateur que j'autorise</caption>
		<?php do { ?>
		<tr>
			<td><?php echo $do->adherent ?></td>
			<td>[<a href="autorise-form.php?ids=<?php echo $do->id ?>" >X</a>]</td>
		<tr>
		<?php }while($do = $jautorise->parcours() ) ?>
	</table>
<?php else : ?>
	<p>Vous n'autorisez personne à modifier vos événements.</p>
<?php endif ?>

<?php if($do = $jsuisautorise->parcours() ) : ?>
	<table class="table_defaut" >
		<caption>Utilisateur qui m'autorise</caption>
		<?php do { ?>
		<tr>
			<td><?php echo $do->adherent ?></td>
		<tr>
		<?php  } while($do = $jsuisautorise->parcours() ) ?>
	</table>
<?php else : ?>
	<p>Personne ne vous autorise à modifier ses évenements.</p>
<?php endif ?>

<?php include BAS_ADMIN; ?>
