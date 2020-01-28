<?php

function remarquable_init($id)
{
	$donne = req('
		SELECT r.id, r.titre, r.texte `desc`, r.lat, r.lng `long`, r.contact contact__id, r.tel, 
			r.site, r.mail, r.type, r.adr, r.ville ville__id, 
			l.Lieu_Ville ville__nom, l.Lieu_Dep ville__dep__num
		FROM remarquable r
		LEFT OUTER JOIN structure_contact sc
			ON sc.id = r.contact 
		LEFT OUTER JOIN Lieu l 
			ON l.Lieu_ID = r.ville 
		WHERE r.id='.(int)$id.' 
	'); 

	if( $do = fetch($donne) )
	{
		$ob = new remarquable(genere_init($do) ); 

		return $ob; 
	}
	else
	{
		return FALSE; 
	}
}
