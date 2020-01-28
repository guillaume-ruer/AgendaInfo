<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<title>Agenda Dynamique <?php echo $title ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<meta name="keywords" content="agenda, <?php echo $meta ?>association info limousin, plateforme diffusion, tourisme, actualite, vie association, tous les thèmes " />
	<meta name="description" content="Agenda : spectacles, rencontres sportives, horaires des marchés, sorties nature, brocantes, feux d&#039;artifice, réunions associations, fêtes locales, animations enfants, soirées jeux" />

    <meta name="viewport" content="width=device-width, initial-scale=1">

	<?php $PAT->affiche_meta()  ?>

	<?php $PAT->affiche_style() ?>

	<script type="text/javascript" >
	var ROOT_PATH = '<?php echo ADD_SITE_DYN ?>../'; 
	<?php $PAT->aff_javascript() ?>
	</script>

	<?php $PAT->affiche_script() ?>

	<?php $PAT->affiche_link() ?>

</head>
<body>
