<?php
/*
	Sort le code fr_FR, en_US etc. qui se trouve dans l'entete 
	-> A modifier au fur et à mesure.
*/

function accept_language()
{
	if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
	{
		return FALSE;
	}

	$entete = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	$tab_langue = array_map('trim', explode(',', $entete) );
	$q = 0;

	foreach($tab_langue as $chaine)
	{
		if(preg_match('`(.+);q=(1|0\.[0-9])`', $chaine, $num ) )
		{
			if($q < $num[2] )
			{
				$q = $num[2];
				$langue = $num[1];
			}
		}
		else
		{
			$langue = $chaine;
			break;	
		}
	}

	$tab_equivalent = array(
		'fr' 	=> 'fr_FR.utf8',
		'fr-fr' => 'fr_FR.utf8',
		'fr-FR' => 'fr_FR.utf8',
		'en'	=> 'en_US.utf8',
		'en-us'	=> 'en_US.utf8',
		'de' 	=> 'de_DE.utf8',
		'de-de' => 'de_DE.utf8'
	);

	if(isset($tab_equivalent[ $langue ]) )
	{
		return $tab_equivalent[ $langue ];
	}
}


?>
