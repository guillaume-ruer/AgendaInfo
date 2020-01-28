<?php
require_once '../../include/init.php'; 
require_once C_INC.'reqa_class.php'; 
require_once C_INC.'adherent_class.php'; 
require_once C_INC.'structure_class.php'; 
require_once C_INC.'contact_class.php'; 
require_once C_INC.'contact_fonc.php'; 
require_once C_INC.'fonc_upload.php'; 
require_once C_INC.'fonc_memor.php'; 
require_once C_INC.'structure_form.php'; 

ajt_style('util_form.css'); 
define('STR_AUCUNE', 0);
define('STR_NOUVEAU', 1);
define('STR_EXISTANTE', 2); 

http_param(array( 'u' => 0, 'p' => 0, 'rech' => '') ); 

$nouveau = empty($u); 
$m = new membre; 
$m->id = $u; 
$traitement = FALSE;
$str_form = new structure_form; 

if(isset($_POST['ok']) )
{
	$traitement= TRUE;
	http_param(array('u_login' => '', 'u_nom'=>'', 'u_prenom' => '', 'u_mdp' => '', 'droit' => array(), 'u_str'=>0, 'og_page_0'=>STR_NOUVEAU) ); 

	$m->droit = 0; 
	foreach($droit as $num )
	{
		$m->droit |= $TAB_DROIT[$num]['bit']; 	
	}
	
	$m->login = $u_login;

	if(!empty($u_mdp) )
	{
		$m->mut_mdp($u_mdp); 
	}

	$m->nom = $u_nom;
	$m->prenom = $u_prenom; 


	switch( $og_page_0 )
	{
		case STR_AUCUNE :
			$m->id_structure = NULL; 
		break; 
		case STR_NOUVEAU : 
			if( $str_form->valide() )
			{
				$str = $str_form->donnee(); 
			}
			else
			{
				$traitement=FALSE; 
			}
		break;
		case STR_EXISTANTE :
			$m->id_structure = empty($u_str)?NULL:$u_str;
		break; 
	}

	if( $traitement )
	{
		if( $og_page_0 == STR_NOUVEAU)
		{
			$PAT->ajt_mess('Nouvelle structure crée.'); 
			str_relais_enr($str); 
			$m->id_structure = $str->acc_id(); 
		}

		$m->enregistre(); 	

		if( $og_page_0 == STR_NOUVEAU )
		{
			req('INSERT INTO structure_droit(utilisateur, structure, droit) 
				VALUES( '.$m->id.', '.$str->acc_id().', '.(STR_MODIFIER|STR_DROIT|STR_EVENEMENT).') ');
		}

		$PAT->ajt_mess('Enregistrement de l\'utilisateur effectué.'); 
	}
}
elseif(!$nouveau )
{
	$m->init($u); 
/*
	$lss = new reqa('
		SELECT sd.droit, s.nom, s.id
		FROM structure_droit sd
		LEFT JOIN structure s
			ON s.id = sd.structure
		WHERE utilisateur='.$m->id.'
		AND droit != 0
	');
*/
}

$ls_pstr = new reqa('
	SELECT nom, id
	FROM structure 
	ORDER BY nom
'); 

$str_defaut = empty($m->id_structure) ? STR_NOUVEAU : STR_EXISTANTE; 
$url_rech = urlencode($rech); 

include HAUT_ADMIN;
?>
<h1><?php if($nouveau) : ?>Créer un<?php else : ?>Edition d'un<?php endif ?> utilisateur</h1>

<?php include 'patron/menu_util.php' ?>

<?php $PAT->affiche_mess() ?>

<?php if(!$traitement) : ?>

<form action="utilisateur-form.php" method="post" enctype="multipart/form-data" >

	<fieldset>
		<legend>Utilisateur</legend>
		<fieldset>
			<legend>Info</legend>
			<table>
				<tr>
					<td><label for="u_prenom" >Prénom : </label></td>
					<td><input id="u_prenom" type="text" name="u_prenom" value="<?php echo $m->prenom ?>" /></td>
				</tr>
				<tr>
					<td><label for="u_nom" >Nom : </label></td>
					<td><input id="u_nom" type="text" name="u_nom" value="<?php echo $m->nom ?>" /></td>
				</tr>
				<tr>
					<td><label for="u_login" >Login : </label></td>
					<td><input id="u_login" type="text" name="u_login" value="<?php echo $m->login ?>" /></label></td>
				</tr>
				<tr>
					<td><label for="u_mdp" >Mot de passe ( non modifié si laissé vide ): </label></td>
					<td><input id="u_mdp" type="password" name="u_mdp" value="" /></td>
				</tr>
			</table>

		</fieldset>
			<?php include 'patron/case-droit.php' ?>
	</fieldset>

	<fieldset>
		<legend>Structure principale</legend>

		<p>Cocher la bonne option et remplisser les champs correspondant.</p>

		<div class="og_groupe" >	
			<div class="og_page" >
				<div class="og_titre" >
					<input id="og_page_0_0" type="radio" name="og_page_0" value="<?php echo STR_AUCUNE ?>" />
					<label for="og_page_0_0" >Pas de structure</label>
				</div>
				<p>L'utilisateur n'aura pas de structure principale.</p>
			</div>
			<div class="og_page" >
				<div class="og_titre" >
					<input id="og_page_0_1" type="radio" name="og_page_0" value="<?php echo STR_EXISTANTE ?>" 
						<?php if($str_defaut == STR_EXISTANTE ) : ?>checked="checked"<?php endif ?> />
					<label for="og_page_0_1" >Structure existante</label>
				</div>

				<p>Si vous modifiez la structure principale, vérifiez que les droits sont corrects.</p>

				<select name="u_str" >
					<?php while($pstr = $ls_pstr->parcours() ) : ?>
					<option value="<?php echo $pstr->id ?>" <?php selected( $pstr->id == $m->id_structure ) ?> >
						<?php echo $pstr->nom ?>
					</option>
					<?php endwhile ?>
				</select>
			</div>
			<div id="nstr" class="og_page" >	
				<div class="og_titre" >
					<input id="og_page_0_2" type="radio" name="og_page_0" value="<?php echo STR_NOUVEAU ?>" 
						<?php if($str_defaut == STR_NOUVEAU ) : ?>checked="checked"<?php endif ?> />
					<label for="og_page_0_2" >Nouvelle structure</label>
				</div>
				<?php $str_form->affiche() ?>
			</div>
		</div>
	</fieldset>
	<script type="text/javascript" >
	$(function(){
		$(".og_page").each(function(i){
			if( $('.og_titre input[type=radio]', this).is(':not(:checked)') )
			{
				$(this).hide(); 
			}
		});	

		$(".og_groupe").each(function(i){
			$(this).prepend( $('<div>') ); 
		});

		$(".og_groupe .og_page .og_titre").each( function(i){ 
			var p = $(this).parents(".og_groupe")[0]; 
			$(this).appendTo( $(':first', p) );
		}); 

		$(".og_titre").css("float", "left"); 
		$(".og_groupe .og_page").css('clear', 'both'); 

		$(".og_titre input ").click(function() {
			var p = $(this).parents(".og_groupe")[0]; 
			$("> .og_page", p).hide(); 
			$("> .og_page:eq("+ $(this).parent().index()+")", p).fadeIn(); 
		});   

	});

	</script>

	<p><input type="hidden" name="u" value="<?php echo $m->id ?>" />
	<input type="hidden" name="rech" value="<?php echo $rech ?>" />
	<input type="hidden" name="p" value="<?php echo $p ?>" />
	<input type="submit" name="ok" value="Ok !" /></p>
</form>

<?php endif ?>

<?php include BAS_ADMIN ?>
