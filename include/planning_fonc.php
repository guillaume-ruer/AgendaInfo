<?php
/*
[
	[
		numéro du mois,
		année du mois,
		nom en français,
		tableau de jour : [ 
			[
				numéro du jour, 
				nombre d'événement,
				weekend ou nom,
				première lettre du jour du jour en français,
				date Y-m-d,
				date passé ou non 
			],
			...
		]
	],
	...
]
*/

function tab_mois($tab_date=array() )
{
	$d = date('n'); 
	$a = date('Y'); 

	for($i=0; $i<4; $i++ )
	{
		$m = $d+$i; 
		$af = $a; 
		if( $m > 12 )
		{
			$af = $a+1; 
			$m %= 12; 
		}

		$f = date('t', mktime(0,0,0,$m,1,$af) ); 
		$jours = []; 

		for($j=1; $j<=$f ; $j++ )
		{
			$time = mktime(0,0,0,$m,$j,$af);
			$date = date('Y-m-d', $time ); 
			$nbe = isset($tab_date[$date]) ? (int)$tab_date[$date] : ''; 
			$jours[] = [$j, $nbe, 
				date('N', $time )>=6, 
				strtoupper( jr_num2str(strftime('%w', $time ) )[0] ), 
				$date, 
				$time>=mktime(0,0,0,date('m'),date('d'),date('Y')),
				date('N', $time) 
			];
		}

		$tabm[] = [$m, $af, ucfirst(moi_num2str($m) ) , $jours ]; 
	}

	return $tabm; 
}

function planning_add_stat($lse=[])
{
	$date = (int)time(); 
	$user_agent = (string)$_SERVER['HTTP_USER_AGENT'];
	$ip = (string)$_SERVER['REMOTE_ADDR']; 

	$pre = prereq('INSERT INTO planning_stat(`date`, user_agent, ip)VALUES(?,?,?)'); 
	exereq($pre, [$date, $user_agent, $ip]); 
	$ids = derid(); 
	$tab_id = []; 

	foreach($lse as $ev)
	{
		$tab_id[] = '('.(int)$ev->acc_id().','.(int)$ids.')'; 
	}

	if( !empty($tab_id) )
	{
		req($v='INSERT INTO planning_stat_event(id_event, id_planning_stat)VALUES'.implode(',',$tab_id) );
	}
}
