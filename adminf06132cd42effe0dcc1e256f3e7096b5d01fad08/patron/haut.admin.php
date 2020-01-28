<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
	   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" >
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<title><?php echo (isset($TITRE) ) ? $TITRE : NOM_SITE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!--	<link rel="Shortcut Icon" type="image/x-icon" href="" /> -->
	<link type="text/css" rel="stylesheet" href="<?php echo C_STYLE; ?>admin.css" />
	<?php if( MODE_DEV ) : ?>
	<link type="text/css" rel="stylesheet" href="<?php echo C_STYLE; ?>mode_dev.css" />
	<?php endif ?>

	<script type="text/javascript" >
	var ROOT_PATH = '<?php echo RETOUR ?>'; 
	</script>

	<?php pscript() ?>
	<?php pstyle() ?>

</head>
<body>

<?php include C_ADMIN.'patron/menu_admin.php' ?>
