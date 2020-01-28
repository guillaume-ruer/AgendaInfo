<?php
if(!function_exists('rappel') )
{
	include C_INC.'fonc_memor.php'; 
}

$image = secuhtml(rappel('fond_infolimo') );

if(empty($image) )
{
	$image = C_DESIGN.'infolimo-fond3.jpg';
}
else
{
	$image = C_DOS_PHP.'image_fond/'.$image; 
}


?>

<style type="text/css" >
	body
	{
		background-image:url('<?php echo $image; ?>'); 
	}
</style>
