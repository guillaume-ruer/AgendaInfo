<?php

if( ! droit(CHANGER_FOND)  )
{
	include PAT_ERREUR; 
	exit(); 
}
