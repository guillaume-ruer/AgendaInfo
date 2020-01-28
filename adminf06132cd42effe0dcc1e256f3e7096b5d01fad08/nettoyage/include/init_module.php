<?php

if( ! droit(PURGE ) )
{
	include PAT_ERREUR; 
	exit(); 
}
