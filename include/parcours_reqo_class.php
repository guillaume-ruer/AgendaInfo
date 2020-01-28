<?php
class parcours_reqo 
{
	function __construct($do)
	{
		foreach( $do as $cle => $val )
		{
			$att = strtolower($cle);
			$this->$att = $val; 
		}
	}
}
