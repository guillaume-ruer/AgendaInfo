<?php

define('TPS', microtime(TRUE) );

$xsl_filename = 'trans.xsl'; 
$xml_filename = 'doc_xml.xml';

include 'conf.php';

$theme = ( isset($_POST['theme']) AND isset($tab_theme[ $_POST['theme'] ]  ) ) ? $_POST['theme']  : '' ;

$doc = new DOMDocument();
$xsl = new XSLTProcessor();

$doc->load($xsl_filename);
$xsl->importStyleSheet($doc);
$xsl->setParameter('', 'idtheme', $theme);

$doc->load($xml_filename);



?>
<html>  
	<head> 
		<title>Mes événements bidons</title>  
		<style type="text/css">  
			th {background-color:silver;} 
			td {border-style:solid; border-width:1px;} 
		</style>  
	</head>  
	<body>  
		<form action="php_trans.php" method="post"  >
			<p><select name="theme" >
			<?php foreach($tab_theme as $id => $nom ) : $s = ($id == $theme) ? ' selected="selected" ' : '';  ?>
				<option value="<?php echo $id; ?>" <?php echo $s; ?> ><?php echo $nom; ?></option>
			<?php endforeach; ?>
			</select>
			<input type="submit" name="ok" value="Ok !" />
			</p>

		</form>
		<h2>Bibliotheque</h2>  
		<table>  	
			<tr>  
				<th>id</th>
				<th>date</th>
				<th>Titre</th>  
				<th>Auteur</th>  
				<th>Theme</th>
			</tr>  

			<?php echo $xsl->transformToXML($doc); ?>
		</table>  
	</body>  
</html> 

<?php
echo round( (microtime(TRUE)-TPS ) *1000, 2 );
?>


