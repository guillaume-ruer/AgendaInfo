<?php

function insert_phrase($dim, $phrase )
{
	$pre = prereq('INSERT INTO phrase_mail(dim, phrase ) VALUES ( ?,?) ');
	exereq($pre, array($dim, $phrase ) ); 
	mess('Phrase inséré.'); 
}

function sup_phrase($id)
{
	req('DELETE FROM phrase_mail WHERE id='.(int)$id.' LIMIT 1 '); 
	mess('Phrase supprimé.'); 
}

function mod_phrase($id, $dim, $phrase )
{
	$pre = prereq('UPDATE phrase_mail SET dim=?, phrase=? WHERE id=?'); 
	exereq($pre, array($dim, $phrase, $id ) ); 
	mess('Phrase modifié.'); 
}

function donne_phrase($id)
{
	$donne = req('SELECT * FROM phrase_mail WHERE id='.(int)$id.' LIMIT 1 ');
	$do = fetch($donne); 
	return $do; 
}

function liste_phrase()
{
	$donne = new reqa('SELECT absint::id, phrase_mail_balise::dim, phrase_mail_balise::phrase FROM phrase_mail ORDER BY id'); 
	return $donne; 
}

function chaine_javascript($p)
{
	$res = ''; 
	$p = explode("\n", $p ); 

	foreach( $p as $c )
	{
		$res .= trim($c).' \n'; 
	}
	return addcslashes($res, '"' );
}

function phrase_mail_balise($p)
{
	return strip_tags($p); 
}
