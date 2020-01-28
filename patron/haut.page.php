<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
	   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<title><?php echo (isset($titre) ) ? $titre : NOM_SITE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!--	<link rel="Shortcut Icon" type="image/x-icon" href="" /> -->
	<link type="text/css" rel="stylesheet" href="<?php echo C_STYLE; ?>style.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo (isset($STYLE) ) ? C_STYLE.$STYLE : ''; ?>" />
	<?php echo (!empty($HEAD) ) ? $HEAD : '' ; ?>
	<?php include C_INC.'fond.php'; ?>
</head>
<body>

