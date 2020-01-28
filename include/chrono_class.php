<?php
/*
	
	$c1 = new chrono; 

	//(re)démarre le chrono
	$c1->deb(); 
	
	//Met le chono sur pause
	$c1->pause(); 

	//donne le temps total
	$c1->fin(); 

*/

class chrono
{
	private $deb = 0; 
	private $tot = 0;
	private $en_route = TRUE; 

	function deb()
	{
		$this->deb = microtime(TRUE);
		$this->en_route = TRUE;
	}

	function pause()
	{
		$this->tot += ( microtime(TRUE) - $this->deb ) * 1000;
		$this->en_route = FALSE;
	}

	function fin()
	{
		if($this->en_route)
		{
			$this->pause(); 
		}

		return round($this->tot, 2); 
	}
}


