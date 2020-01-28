<?php
include 'mail_class.php'; 
$bool=FALSE; 
$sujet = $mess = '' ;
if(isset($_POST['ok']) )
{
	mel('strategeyti@gmail.com', $sujet = stripslashes($_POST['sujet']), $mess = stripslashes($_POST['mess']) ); 
    $bool = TRUE; 
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF8" />
	<title>Envoyer un mail</title>
</head>
<body>
<h1>Envoyer un mail</h1>

<?php if( $bool ) : ?>
    <p>Message envoyé !<p>
<?php endif ?>
<form action="" method="post" >
	<p>Titre : <input type="text" name="sujet" value="<?php echo $sujet ?>" /></p>
	<p>Message : <textarea name="mess" rows="7" cols="70" ><?php echo $mess ?></textarea></p>
	<p><input type="submit" name="ok" value="Envoyer !" /></p>
</form>
</body>
</html>
