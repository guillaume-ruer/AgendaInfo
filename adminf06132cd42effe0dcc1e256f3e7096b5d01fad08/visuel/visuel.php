<?php
include '../../include/init.php'; 
include C_INC.'reqa_class.php'; 
include C_INC.'visuel_fonc.php'; 
include C_INC.'visuel_class.php'; 

http_param(array('id' => 0, 'p' => 0, 't'=>0 ) ); 


if( !isset($VISUEL_CONF[ $t ]) )
{
	page_erreur(); 
}
elseif( !empty($VISUEL_CONF[ $t ]['droit']) && !droit(GERER_VISUEL) )
{
	page_erreur(); 
}


/*
	Suppression du visuel 
*/

if(!empty($id) )
{
	if( visuel::sup($id) )
	{
		mess('Suppression effectué.'); 
	}
	else
	{
		mess('Vous n\'avez pas les droits necessaire pour supprimer ce visuel.');
	}
}

/*
	init 
*/

$aff = new visuel_liste($VISUEL_CONF, $t); 
$aff->page = $p;
$aff->nb = 10; 
$aff->pagin = TRUE; 
$aff->droit = TRUE; 
$aff->fi_date = visuel_liste::D_FUTURE;
$aff->ordre = visuel_liste::O_DATE;
$donne = $aff->requete(); 

$VT = $VISUEL_CONF[ $aff->acc_type() ];

include HAUT_ADMIN;
?>

<h1><?php echo $VT['titre'] ?></h1>

<?php pmess() ?>

<p><a href="visuel-form.php?t=<?php echo $t ?>" ><?php echo $VT['nouveau'] ?></a></p>

<p><?php echo $VT['texte'] ?></p>

<?php if($donne->nb_page > 1 ) : ?>
	<p>
	<?php for($i=0;$i<$donne->nb_page;$i++) : ?>
		<a href="visuel.php?p=<?php echo $i ?>&amp;t=<?php echo $t ?>" class="<?php echo $i==$p ? 'actif' : 'inactif' ?>" ><?php echo $i+1 ?></a>
	<?php endfor ?>
	</p>
<?php endif ?>

<table class="table_defaut" >
	<tr>
		<?php if($VT['contenu'] == TRUE ) : ?>
		<th>Titre</th>
		<th>Texte</th>
		<?php endif ?>
		<th>Image</th>
		<?php if($VT['date'] == TRUE ) : ?>
		<th>Début de la diffusion</th>
		<th>Fin de la diffusion</th>
		<?php endif ?>
		<th>Affichages</th>
		<th>Clics</th>
		<th></th>
	</tr>
<?php while($do = $donne->parcours() ) : ?>
	<tr class="<?php if($donne->switch) : ?>ligne1<?php else : ?>ligne2<?php endif ?>" >
		<?php if($VT['contenu'] == TRUE ) : ?>
		<td><?php echo $do->titre ?></td>
		<td><?php echo $do->texte?></td>
		<?php endif ?>
		<td><img src="<?php echo C_DOS_PHP.$VT['dos'].$do->img ?>" alt="Image" /></td>
		<?php if($VT['date'] == TRUE ) : ?>
		<td><?php echo date_format_fr($do->datedeb) ?></td>
		<td><?php echo date_format_fr($do->datefin) ?></td>
		<?php endif ?>
		<td><?php echo $do->aff ?></td>
		<td><?php echo $do->clic ?></td>
		<td>
		<?php if(droit(GERER_UTILISATEUR) || $do->droit) : ?>	
		<a href="visuel-form.php?id=<?php echo $do->id ?>&amp;t=<?php echo $t ?>" >Modifier</a><br />
		<a href="visuel.php?p=<?php echo $p ?>&amp;id=<?php echo $do->id ?>&amp;t=<?php echo $t ?>" 
			onclick="return confirm('Voulez vous vraiment supprimer ce visuel ?');" >Supprimer</a>
		<?php endif ?>
		</td>
	</tr>
<?php endwhile ?>
</table>

<?php include BAS_ADMIN ?>
