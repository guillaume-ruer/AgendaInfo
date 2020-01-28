<!DOCTYPE html >
<html>
<head>
	<title>Condition</title>
	<meta http-equiv="content-type" content="text/html; charset=utf8" />
</head>
<body>

<h1>Les conditions</h1>

<p>Comparer le fichier source avec ce qui est affiché sur votre navigateur.</p>

<h3>Code uniquement php</h3>

<?php
if( TRUE )
{
	echo '<p>La condition est vrai.</p>'; 
}
else
{
	echo '<p>La condition est fausse.</p>'; 
}
?>

<h3>Code avec balise html</h3>

<?php if(TRUE ) { ?>
	<p>La condition est vrai.</p>
<?php } else { ?>
	<p>La condition est fausse.</p>
<?php } ?>

<h3>Syntaxe alternative</h3>

<p>Le code précédent n'est pas très lisible, c'est pourquoi je préfère utiliser la syntaxe alternative dans mes codes html.</p>

<?php if( TRUE ) : ?>
	<p>La condition est vrai.</p>
<?php else : ?>
	<p>La condition est fausse.</p>
<?php endif ?>

<h3>Pratique</h3>

<p>Remplacer les "TRUE" par "FALSE", "0" et/ou "1" dans le fichier source, et actualiser la page.</p>

<h3>Remarques</h3>

<p>On voit que ces trois écritures font exactement la même chose.</p>

</body>
</html>
