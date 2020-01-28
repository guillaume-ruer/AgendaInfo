<?php
function location_stat_rss($id)
{
	req('UPDATE Externe SET nb_rss = nb_rss + 1 WHERE id='.(int)$id.' LIMIT 1 ');
}

function location_stat_ext($do)
{
	// Page appelante 
	if( !isset($_SERVER['HTTP_REFERER']) )
		return FALSE; 

	$s = $_SERVER['HTTP_REFERER']; 

	if(!empty($do['page_appelante']) )
	{
		if( !($tab_pa = unserialize($do['page_appelante']) ) ) 
		{   
			$tab_pa = array(); 
		}   
	} 
	else
	{
		$tab_pa = array(); 
	}

	if( !preg_match('`^(http://)?((www\.)?info-limousin)|(localhost)`', $s) && !in_array($s, $tab_pa) )
	{
		$tab_pa[] = $s; 
	}

	while( count($tab_pa) > 10 )
	{
		array_shift($tab_pa); 
	}

	// Conteur 
	req('UPDATE Externe SET nb_ext = nb_ext + 1, page_appelante=\''.secubdd(serialize($tab_pa)).'\' WHERE id='.(int)$do['id'].' LIMIT 1 ');
}

