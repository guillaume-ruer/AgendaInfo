<?php

class message extends objet
{
	protected $message=array(); 

	function init()
	{
		$this->message = array(); 
	}

	function ajt($m, $class="message" )
	{
		$this->message[] = array('message' => $m, 'class' => $class); 	
	}

	function aff()
	{
		foreach($this->message as $m )
		{
			echo "\n<p class=\"{$m['class']}\" >{$m['message']}</p>\n"; 
		}
	}

	function aff_ligne()
	{
		foreach($this->message as $m )
		{
			echo "\n<span class=\"message_ligne {$m['class']}\" >{$m['message']}</span>\n"; 
		}
	}

	function fusion(message $m )
	{
		if( !is_null($m) )
		{
			$this->message = array_merge($this->message, $m->acc_message() ); 
		}
	}

	function ajt_class($class)
	{
		foreach($this->message as $m )
		{
			$m['class'] .= ' '.$class; 
		}
	}
}
