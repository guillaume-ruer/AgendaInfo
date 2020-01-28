<?php

function flux_rss($lien)
{
	$d = new DOMDocument();
	$d->load($lien); 
	$nl = $d->getElementsByTagName('item'); 
	$tab_element = array(); 

	foreach($nl as $n )
	{
		$tab = array(); 
		$cn = $n->childNodes; 

		foreach($cn as $c )
		{
			if(isset($c->tagName) && in_array($c->tagName, array('title', 'description', 'link', 'pubDate') ) )
			{
				$tab[$c->tagName] = $c->nodeValue; 
			}
		}

		$tab_element[] = $tab; 
	}

	return $tab_element; 
}
