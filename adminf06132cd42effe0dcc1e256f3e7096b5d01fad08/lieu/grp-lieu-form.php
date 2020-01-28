<?php
include '../../include/init.php'; 
include C_INC.'lieu_ls.php'; 
include C_INC.'lieu_grp_class.php'; 
include C_INC.'lieu_grp_fonc.php'; 

http_param( array('id' => 0) ); 

$traitement = FALSE; 

// Champ de séléction de lieu 
$ch_lieu = new barre_proposition_form(['fichier'=> 'lieu.php', 'class' => 'ville', 'label' => 'Communes'] ); 

if( isset($_POST['ok']) )
{
	http_param( array('nom'=>'', 'ordre'=>2, 'num' => '' ) ); 

	$do_lieu = $ch_lieu->donne(); 

	$lieu = []; 

	foreach($do_lieu as $l )
	{
		$lieu[] = $l->id(); 
	}

	$grp = new lieu_grp( array(
		'id' => $id, 
		'nom' => $nom,
		'tab_lieu' => $lieu, 
		'ordre' => $ordre, 
		'num' => $num,
	) ); 

	$var = lieu_grp_crud();
	$var->enr($grp); 
	lieu_grp_enr_lieu($grp); 
	$traitement = TRUE; 
}
else
{
	$grp = lieu_grp_init($id); 

	$tlieu = new lieu_ls(['fi_groupe' => $grp->acc_id() ]); 
	$tlieu->requete(); 
	$tch = []; 

	while ( $l = $tlieu->parcours() )
	{
		$tch[] = $l;  
	}

	$ch_lieu->mut_donne($tch); 

	$tab_ordre = array(1=>'Departement', 2=>'Pays/PNR', 3=>'CA', 4=>'CC'); 
}

require PATRON; 
