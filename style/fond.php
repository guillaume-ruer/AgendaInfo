<?php
include '../include/init.php'; 
header('content-type: text/css'); 

if(!function_exists('rappel') )
{
	include C_INC.'fonc_memor.php'; 
}

$image = secuhtml(rappel('fond_infolimo') );

if(!empty($image) ) : ?>
body
{
	background-repeat:no-repeat; 
	background-image:url('<?php echo RETOUR.'dos-php/image_fond/'.$image; ?>'); 
}
<?php endif ?>
