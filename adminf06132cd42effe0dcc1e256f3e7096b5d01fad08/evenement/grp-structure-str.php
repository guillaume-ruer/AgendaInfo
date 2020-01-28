<?php
require '../../include/init.php'; 

$groupe = FALSE; 
http_param(array('id' => 0) ); 
if( !empty($id) )
{
	$donne = req('SELECT * FROM structure_grp WHERE id='.(int)$id.' LIMIT 1 '); 
	
	if( $grp = fetch($donne) ) 
	{
		$groupe = TRUE; 
	}
}

if( !$groupe )
{
	header('Location: grp-structure.php'); 
	exit(); 
}

$form = new liste_form(); 
$form->ajt('structure', 'barre_proposition', 'Rechercher des structures', array('class' => 'structure', 'fichier'=>'../'.D_ADMIN.'ajax/structure-ls.php') ); 
$valide = FALSE; 

if( isset($_POST['ok']) )
{
	if( $form->verif() )
	{
		$donne = $form->donne(); 

		req('DELETE FROM structure_grp_structure WHERE id_structure_grp='.(int)$grp['id'].' '); 
		$pre = prereq('INSERT INTO structure_grp_structure(id_structure, id_structure_grp)VALUES(?,?) '); 

		foreach($donne['structure'] as $str)
		{
			exereq($pre, array($str->id(), $grp['id']) ); 	
		}

		$valide = TRUE; 
	}
}
else
{
	$donne = req('
		SELECT s.id, s.nom
		FROM structure_grp_structure sg
		JOIN structure s
			ON s.id = sg.id_structure
		WHERE sg.id_structure_grp='.(int)$grp['id'].'
	'); 
	$tab = array(); 
	while($do = fetch($donne) )
	{
		$tab[] = new structure($do); 
	}

	$form->acc('structure')->donne = $tab; 
}


require PATRON; 
