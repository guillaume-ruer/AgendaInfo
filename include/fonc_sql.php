<?php
$NB_REQ = 0; 
$NB_EXE = 0; 
$NB_PRE = 0; 
$TAB_REQUETE = []; 

define('SQL_ROW', 1); 
define('SQL_ARRAY', 0); 
define('SQL_IGNORE_ERREUR', 0x1); 

function req($sql, $opt=0 )
{
	global $BDD, $NB_REQ, $TAB_REQUETE; 

	if( MODE_DEV ) tps(); 

	if( !( ($donne = $BDD->query($sql) ) || ($opt & SQL_IGNORE_ERREUR) ) )
	{
		$tab_err = $BDD->errorInfo();
		$erreur = "ERREUR SQL\n"; 
		$erreur .= $tab_err[2]; 
		$erreur .= '<div>'.$sql.'</div>'; 
		debug($erreur, DEBUG_BACKTRACE_TRUE, ERREUR_PHP_TRUE); 
	}

	if( MODE_DEV ) $TAB_REQUETE[] = array('type'=>'req', 'tps' => tps(), 'req' => $sql, 'err' => !$donne ); 	

	$NB_REQ++; 
	return $donne; 

}

function fetch( $donne, $fetch = PDO::FETCH_ASSOC )
{
	if( $donne )
	{
		return $donne->fetch($fetch); 
	}
}


function prereq($sql)
{
	global $BDD, $NB_PRE, $TAB_REQUETE; 
	static $tab_pre=array(), $tab_sql=array();// Buffer  

	if( ($id = array_search($sql, $tab_sql) ) !== FALSE )
	{
		return $tab_pre[$id]; 
	}
	else
	{
		if( MODE_DEV ) tps(); 
		if( ! ( $donne = $BDD->prepare($sql) ) ) 
		{
			debug($sql, DEBUG_BACKTRACE_FALSE, ERREUR_PHP_TRUE); 
			debug($BDD->errorInfo() ); 
		}
		if( MODE_DEV ) $TAB_REQUETE[] = array('type' => 'pre', 'tps'=> tps(), 'req' => $sql, 'err' => !$donne ); 	

		$NB_PRE++; 
		$tab_pre[] = $donne; 
		$tab_sql[] = $sql; 
		return $donne; 
	}
}

/*
	param : objet pdostatement, le tableau des marqueurs 
	retour : rien 
	post-condition : l'objet pdostatement a été executé. 
*/

function exereq(&$res, $tab=array(), $opt=0 )
{
	global $NB_EXE, $TAB_REQUETE; 
	$NB_EXE++; 

	if( MODE_DEV ) tps();

	$res_exe =  $res->execute($tab);

	if(! ($res_exe || ($opt & SQL_IGNORE_ERREUR ) ) ) 
	{
		$tab_err_res = $res->errorInfo(); 
		$erreur = "ERREUR SQL\n"; 
		$erreur .= $tab_err_res[1].' '.$tab_err_res[2]."\n"; 
		$erreur .= $res->queryString."\n"; 
		$erreur .= implode('  |  ', $tab)."\n"; 
		debug($erreur, DEBUG_BACKTRACE_TRUE, ERREUR_PHP_TRUE); 
	}

	if( MODE_DEV ) $TAB_REQUETE[] = array('type'=> 'exe', 'req' => $res->queryString, 'tps' => tps(), 'err' => !$res_exe ); 

	return $res_exe; 
}
/*
	param : une requete sql ( sans la limit )
	retour : le nombre de ligne que contiendra la ressource
*/

function nb_entre($sql)
{
	$req_count = req('SELECT COUNT(*) AS nb_entre '.stristr($sql, 'FROM') );
	$tab = $req_count->fetchAll(PDO::FETCH_ASSOC); 
	$nb = count($tab); 
	return  stristr($sql, 'GROUP BY' ) !== FALSE ? $nb : $tab[0]['nb_entre']; 
}

function derid()
{
	global $BDD; 
	return $BDD->lastInsertId(); 
}

function connexion_fin()
{
	$BDD = NULL;
}

function exepre($sql, $tab)
{
	$donne = prereq($sql);
	exereq($donne, $tab);
	return $donne; 
}

function format_value($val)
{
    global $BDD;

    $res = '';

    if( is_int($val) )
    {
        $res = $val;
    }
    elseif( is_float($val) )
    {
        $res = str_replace(',', '.', (string)$val);
    }
    elseif( is_bool($val) )
    {
        $res = $val ? 'true' : 'false';
    }
    elseif( is_null($val) )
    {
        $res = 'NULL';
    }
    elseif( is_array($val) )
    {
        if( isset($val['raw']) )
        {
            $res = $val['raw'];
        }
        else
        {
            debug('Tableau avec "raw" attendu', TRUE, TRUE);
        }
    }
    else
    {
        $res = $BDD->quote($val);
    }

    return $res;
}

function clause_in($tab)
{
    $res = array_map(function($val){
        return "'".$val."'";
    }, $tab );
    return implode(',', $res);
}

function clause_value($tab)
{
    $res = array_map('format_value', $tab );
    return implode(',', $res);
}

function maj_tab_sql($table, $tab, $id)
{
    $tmp = []; 
    foreach($tab as $cle => $val )
    {
        $tmp[] = "$cle=".format_value($val);
    }

    $sql = 'UPDATE '.$table.' SET '.implode(',', $tmp).' WHERE id='.$id;
    return $sql;
}


function ins_tab_sql($table, $tab)
{
    $req = 'INSERT INTO '.$table.'('.implode(', ', array_keys($tab) ).') VALUES('.clause_value($tab).')';
    return $req;
}

function ins_tab($table, $tab)
{
    req(ins_tab_sql($table, $tab) );
}

