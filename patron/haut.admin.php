<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
	   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" >
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<title><?php echo (isset($titre) ) ? $titre : NOM_SITE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!--	<link rel="Shortcut Icon" type="image/x-icon" href="" /> -->
	<link type="text/css" rel="stylesheet" href="<?php echo C_STYLE; ?>admin.css" />
	<script type="text/javascript" src="<?php echo RETOUR; ?>javascript/menu_deroulant.js"></script>
	<?php echo (isset($HEAD) ) ? $HEAD : '' ; ?>
	<?php foreach($TAB_SCRIPT as $script ) : ?>
		<script type="text/javascript" src="<?php echo RETOUR; ?>javascript/<?php echo $script; ?>"></script>
	<?php endforeach; ?>
	
</head>
<body>

<div id="menu" >

<ul>
	<li>navigation 
		<ul>
			<li><a href="<?php echo RETOUR; ?>" >Retour à info-limousin</a></li>
			<li><a href="<?php echo C_ADMIN; ?>admin.php" >Page d'accueil</a></li>
		</ul>
	</li>
	<li>Evenement 
		<ul>
			<li><a href="<?php echo C_ADMIN; ?>evenement/event.php" >Liste</a></li>
			<li><a href="<?php echo C_ADMIN; ?>evenement/event-form.php" >nouveau</a></li>
		</ul>
	</li>
	<li>Statistique
		<ul>
			<li><a href="<?php echo C_ADMIN; ?>stat/stat.php" >Général</a></li>
			<li><a href="<?php echo C_ADMIN; ?>stat/stat_location.php" >Location</a></li>
		</ul>
	</li>
	<li>LEI
		<ul>	
			<li><a href="<?php echo C_ADMIN; ?>lei/theme-lei.php" >Thèmes</a></li>
			<li><a href="<?php echo C_ADMIN; ?>lei/contact-lei.php" >Contact</a></li>
			<li><a href="<?php echo C_ADMIN; ?>lei/log_lei.php" >Log</a></li>
		</ul>
	</li>
	<li><a href="<?php echo C_ADMIN; ?>fond/fond-form.php" >Image de fond</a></li>
	<li><a href="<?php echo C_ADMIN ?>nettoyage/net.php" >Purge</a></li>

</ul>

</div>

<div id="contenu" >
