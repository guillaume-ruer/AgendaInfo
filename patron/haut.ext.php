<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<title><?php echo (isset($titre) ) ? $titre : NOM_SITE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!--	<link rel="Shortcut Icon" type="image/x-icon" href="" /> -->
	<link type="text/css" rel="stylesheet" href="<?php echo C_STYLE; ?>style_externe.css" /> 
	<link type="text/css" rel="stylesheet" href="<?php echo C_STYLE; ?>style_<?php echo $css_externe; ?>.css" /> 
	<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />

	<script type="text/javascript" src="<?php echo RETOUR; ?>jscalendar/calendar.js"></script>
	<script type="text/javascript" src="<?php echo RETOUR; ?>jscalendar/lang/calendar-en.js"></script>
	<script type="text/javascript" src="<?php echo RETOUR; ?>jscalendar/lang/calendar-fr.js"></script>
	<script type="text/javascript" src="<?php echo RETOUR; ?>jscalendar/calendar-setup.js"></script>

	<script type="text/javascript" src="<?php echo RETOUR; ?>javascript/menu_deroulant.js"></script>
</head>
<body>
