<?php
function genere_init($do)
{
	$init = array();

	foreach( $do as $cle => $val )
	{
		$tab_chemin = explode('__', $cle); 
		$tmp = &$init;

		for( $i=0 ; $i<count($tab_chemin)-1; $i++)
		{
			$tmp = &$tmp[ $tab_chemin[$i] ]; 
		}

		$tmp[ $tab_chemin[$i] ] = $val; 
	}

	return $init; 
}

function entre2mot($mot1, $mot2, $chaine )
{
	if( ($num1 = stripos($chaine, $mot1) ) === FALSE )
	{
		return ''; 	
	}
	
	if( ($num2 = stripos($chaine, $mot2) ) === FALSE )
	{
		return '';
	}

	if($num1 > $num2 )
	{
		$start = $num2 + strlen($mot2);
		$long = $num1 - $start; 
	}
	else
	{
		$start = $num1 + strlen($mot1);
		$long = $num2 - $start; 
	}

	return substr($chaine, $start, $long);
}

/*
	vérifi que la chaine date est au format YYYY-MM-DD
*/

function est_date($date)
{
	return preg_match('`[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}`', $date ); 
}

function est_date_fr($date)
{
	return preg_match('`[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}`', $date ); 
}



/*
	Détruit la session, pour déconnecter un utilisateur
*/

function detruit_session()
{
	unset($_SESSION);
	setcookie(session_name(), '', time()-3600 );
	session_destroy();
}

function http_param_tab($donne )
{
        $tab = array(); 

        foreach( $donne as $nom => $val )
        {   
                if( is_int($nom) )
                {   
                        $nom = $val;
                        $val = ''; 
                }   

                if( isset($_POST[$nom]) )
                {   
                        $res = $_POST[$nom]; 
                        settype($res, gettype($val) );  
                }   
                elseif( isset($_GET[$nom]) )
                {   
                        $res = $_GET[$nom]; 
                        settype($res, gettype($val) );  
                }   
                else
                {   
                        $res = $val; 
                }   

                $tab[] = $res; 
        }   
     
        return $tab; 
}

/*
	param : un tableau array( 'nom_var' => 'valeur par défaut' , [ etc. ] )
	retour : rien 
	ajoute dans l'environnement des variable $nom_var avec pour valeur des donné POST ou GET (si pas de post ) ou 
	la valeur par défaut dans le cas ou aucun n'est trouvé. 
*/

function http_param($tab_param)
{
	foreach($tab_param as $cle => $val )
	{
		if(isset($_POST[ $cle ] ) )
		{
			$v = mm_type($_POST[ $cle ], $val );
		}
		elseif(isset($_GET[ $cle ] ) )
		{
			$v = mm_type($_GET[ $cle ], $val );
		}
		else
		{
			$v = $val;
		}

		$GLOBALS[ $cle ] = $v;
	}
}

/*
	param : la valeur à vérifié, une variable quelconque. 
	retour : la valeur à été casté avec le même type que le deuxieme paramêtre
*/
function mm_type($val, $val_type )
{
	if(is_array($val_type) )
	{
		$val = (array)$val; 
	}
	elseif(is_int($val_type) )
	{
		$val = (int)$val; 
	}
	elseif( is_float($val_type) )
	{
		$val = (float)$val; 
	}
	elseif( is_object($val_type) )
	{
		$val = $val_type($val); 
	}
	else
	{
		$val = (string)$val; 
	}

	return $val; 
}

/*
	fonction pour le bouton retour 
*/
function ancien_url()
{
	return (!isset($_SESSION['der_url']) ) ? RETOUR : $_SESSION['der_url'] ; 
}

/*
	fonction à lancé à la fin du code. 
*/

function memor_url()
{
	$_SESSION['der_url'] = secuhtml( $_SERVER['REQUEST_URI'] ); 
}

/*
	fonction de multilinguisme.
	Suppose qu'un tableau $lang à été initialisé. 

	param : l'index du tableau 
	
	affiche le contenu du tableau ou l'id si rien n'est trouvé. 
*/
function l($id)
{
	global $lang; 
	echo ( isset($lang[ $id ]) ) ? $lang[ $id ] : $id ; 
}


/*
	param : int debut, int fin, int incrément, int numéro par défaut 
	return : une chaine de caractère des balise option. 
*/

function selopt($deb, $fin, $inc,$sel=0)
{
	$opt = '';
	for($i=$deb ; $i<=$fin ; $i +=$inc )
	{
		$s = ($i == $sel) ? 'selected="selected"': '';
		$opt .='<option value="'.$i.'" '.$s.' >'.$i.'</option>';
	}
	
	return $opt;
}


/*
	Recherche et définition des constantes de conf en bdd
*/

function def_var_conf($tab_const)
{
	/*
		Création d'un filtre pour la requete en fonction de tab_const
	*/
	
	$tab_where = array();

	foreach($tab_const as $nom => $val)
	{
		$tab_where[] = "'".$nom."'";
	}

	$donne = req('SELECT * FROM config WHERE nom IN('.implode(',', $tab_where).')');
	while($do = mysql_fetch_assoc($donne) )
	{
		$val = $do['val'];
		$nom = strtoupper($do['nom']);
		$def = $tab_const[$nom];

		/*
			On force le type de la valeur de sorti au même type 
			de la valeur par défaut.
		*/
		if(is_int($def) )
		{
			$val = (int)$val;
		}
		elseif(is_float($def) )
		{
			$val = (float)$val;
		}
		elseif(is_bool($def) )
		{
			$val = (bool)$val;
		}
		
		//On enlève cette entré du tableau
		unset($tab_const[$nom]);
		
		//Définition de la variable
		define($nom, $val);
	}

	/*
		Pour les valeurs non présente en bdd
	*/

	foreach($tab_const as $nom => $defaut )
	{
		define($nom, $defaut);
	}
}
/*
	Récupère des informations sur un utilisateur

	Fichier dans C_LOG 
	Nom de fichier de type log_JJ_MM_AAAA 
	Sur chaque ligne du fichier, séparé par ";" :
	- timestamp 
	- ip
	- id de l'utilisateur 
	- fichier demandé 
	- description de l'action
	- temps de génération de la page 
	- nombre de requete effectué 
*/

$GLOBALS['T_DESC'] = ''; //Trace DESCription : description afficher dans le log

function trace()
{
	global $NB_REQ, $T_DESC; 

	$desc = str_replace(';', ' ', $T_DESC);
	$ip = $_SERVER['REMOTE_ADDR'];
	$id = ID; 
	$t = time();
	$file = $_SERVER['SCRIPT_FILENAME'];
	$tps = round((microtime(TRUE) - TPS ) * 1000, 2 );
	$nb_req = $NB_REQ; 

	$contenu = "$t;$ip;$id;$file;$desc;$tps;$nb_req\n";

	if(!file_exists(C_LOG) ) mkdir(C_LOG); 

	file_put_contents(C_LOG.'log_'.date('j_n_Y'), $contenu, FILE_APPEND);
}


/*
	Retourne un tableau contenant les nom corespondant à la recherche. 

		arg 1 = nom du dossier 
		arg 2 [optionnel] = une chaine de recherche 

*/

function dosimg($chemdos, $rech = '')
{
	$list_img = array();

	$filtre = FALSE;

	if(!empty($rech))
	{
		//On enlève les espace avant et après. 
		$mask = trim($rech);
		
		//On active le filtre si la recherche n'est toujours pas vide.
		if(! ( $mask == '' ) )
		{
			$filtre = TRUE;		
		}
		
		//On remplace les caractère non alphanumérique par un point.
		$mask = preg_replace('#[^ a-z0-9]#i','.', $mask);

		//On remplace les espaces par des pipe.
		$mask = preg_replace('# +#', '|', $mask) ;

	}

	if( $dossier = opendir($chemdos) ) 
	{
		while( ( false !== ($img = readdir($dossier) ) )  )
		{
			$ajt = TRUE;
		
			//Si le filtre est activé
			if($filtre AND !preg_match('#'.$mask.'#i', $img) )
			{	
				$ajt = FALSE;
			}

			if($ajt)
			{
				$list_img[] = $img;
			}

		}

		closedir();
	}

	return $list_img;
}
/*
	Passer un droit en paramêtre
*/

function droit($num_droit)
{
	return (bool)(DROIT & $num_droit); 
}

/*
	Retour à l'index (si par exemple, l'utilisateur n'a rien à faire là
*/

function senva()
{
	header('Location: '.ADD_SITE);
	exit();
}

/*
	Formate la date avec date()	
	Donner un timestamp
*/
function madate($time)
{
	return '<span class="date" >'.date('j/n/Y \à G\hi\m', $time).'</span>';
}


/*
	à donné un tableau de message d'erreur ou confirmation. 
	pmess comme print message
	Appeler sans argument imprime $MESS
*/

$MESS = array(); // Contenu mis en forme et afficher avec pmess()

function mess($mess) 
{
	global $MESS; 
	$MESS[] = $mess;
}

function pmess()
{
	global $MESS;
	$args = func_get_args();

	if(empty($args) )
	{
		$tab = $MESS;
	}
	else
	{
		$tab = (array)$args[0];
	}

	foreach($tab as $mess )
	{
		$class = 'message';
		echo '<p class="'.$class.'" >'.$mess."</p>\n";
	}
}

$TAB_SCRIPT = array(); 

function ajt_script($sc, $dos = C_JAVASCRIPT )
{
	global $TAB_SCRIPT;
	$TAB_SCRIPT[] = $dos.$sc; 
}

function pscript()
{
	global $TAB_SCRIPT; 
	foreach($TAB_SCRIPT as $sc )
	{
		echo '<script type="text/javascript" src="', $sc ,"\" ></script>\n"; 
	}
}

$TAB_STYLE = array(); 

function ajt_style($style, $dos = C_STYLE )
{
	global $TAB_STYLE; 
	$TAB_STYLE[] = $dos.$style; 
}

function pstyle()
{
	global $TAB_STYLE; 
	foreach( $TAB_STYLE as $style )
	{
		echo '<link rel="stylesheet" type="text/css" href="', $style,'" />'; 
	}
}

/*
	Fonction d'echapement des chaine entrant en bdd
*/

function secubdd($chaine)
{
	return addslashes($chaine);
}

/*
	A utilisé sur les donné vennant de la bdd, avant affichage 
*/

function secuhtml($chaine)
{
//	return strip_tags( stripslashes(html_entity_decode($chaine, ENT_QUOTES, 'UTF-8') ) );
	return htmlspecialchars( stripslashes(html_entity_decode($chaine, ENT_QUOTES, 'UTF-8') ), ENT_QUOTES, 'UTF-8');
}

// Print secure
function ps($chaine)
{
	echo secuhtml($chaine); 
}

function absint($chaine)
{
	return abs( (int)$chaine);
}

/*
	Sécurise et formate une chaine venant de la bdd
*/

function fhtml($chaine)
{
	$chaine = secuhtml($chaine);
	$chaine = nl2br($chaine);

	$tab_masque = array(
		'`\[g\](.+)\[/g\]`isU',
		'`\[i\](.+)\[/i\]`isU',
		'`\[s\](.+)\[/s\]`isU',
		'`\[b\](.+)\[/b\]`isU',

		'`\[titre1\](.+)\[/titre1\]`isU',
		'`\[titre2\](.+)\[/titre2\]`isU',

		'`\[bleu\](.+)\[/bleu\]`isU',
		'`\[vert\](.+)\[/vert\]`isU',
		'`\[jaune\](.+)\[/jaune\]`isU',
		'`\[orange\](.+)\[/orange\]`isU',
		'`\[rouge\](.+)\[/rouge\]`isU',
		'`\[violet\](.+)\[/violet\]`isU',
		
		'`\[l url=&quot;(.+)&quot;\](.+)\[/l\]`isU',
		'`\[c nom=&quot;(.+)&quot;\](.+)\[/c\]`isU',

		'`\[image src=(.+) \]`isU',
		'`\[pdf src=(.+) \](.+)\[/pdf\]`isU',
		
		'`\[ac\](.+)\[/ac\]`isU',
		'`\[ad\](.+)\[/ad\]`isU',

		'`\[fg\](.+)\[/fg\]`isU',
		'`\[fd\](.+)\[/fd\]`isU'
	);

	$tab_remplace = array(
		'<span class="gras" >$1</span>',
		'<span class="italique" >$1</span>',
		'<span class="souligne" >$1</span>',
		'<span class="barre" >$1</span>',

		'<span class="titre1" >$1</span>',
		'<span class="titre2" >$1</span>',

		'<span class="c_bleu" >$1</span>',
		'<span class="c_vert" >$1</span>',
		'<span class="c_jaune" >$1</span>',
		'<span class="c_orange" >$1</span>',
		'<span class="c_rouge" >$1</span>',
		'<span class="c_violet" >$1</span>',
		
		'<a href="$1" >$2</a>',
		'<span class="cit_auteur" >Auteur : $1</span>
			<span class="cit_contenu">$2</span>',

		'<img src="$1" alt="$1" />',
		'<a href="$1" >$2</a>',

		'<span class="align_centre" >$1</span>',
		'<span class="align_droite" >$1</span>',
		
		'<span class="flottant_gauche" >$1</span>',
		'<span class="flottant_droite" >$1</span>'
	);

	$chaine = preg_replace($tab_masque, $tab_remplace, $chaine);

	return $chaine;
}

/*
	Dévoile le contenu des variable passer en argument
*/


function imp()
{
	$args = func_get_args();

	foreach($args as $do )
	{
		echo '<pre>';
		var_dump($do);
		echo '</pre>';
	}
}

/*
	A utilisé pour envoyer une requete sql
	si SQL_DEBUG est à TRUE, Un message claire sera afficher pour résoudre une 
	eventuelle erreur.
	Conte également le nombre d'appelle à cette fonction avec $NB_REQ
*/

$GLOBALS['NB_REQ'] = 0; //Nombre de requete sql effectué

/*
	Crée des lien de pagination
*/

function pagin($table, $page, $nbaffiche=10, $cond='', $sep=' ', $get='pg', $aget=''  )
{
	/*
		On recherche le nombre d'entré 
		Avec des conditions si spécifié
	*/
	
	if(!empty($cond) ){ $cond = ' WHERE '.$cond.' '; }

	$donne = req('SELECT COUNT(*) AS nbent FROM '.$table.' '.$cond.' ');
	$nbent = mysql_result($donne, 0);
	
	/*
		Le nombre à affiché est traité, (int) positif
	*/

	$nbaffiche = abs( (int)$nbaffiche );

	if($nbaffiche == 0 ){ $nbaffiche = 1; }
	
	/*
		Calcule du nombre de "pages" 
	*/

	$nbpg = ceil($nbent / $nbaffiche);
	
	/*
		récupération du numéro de la page demandé
	*/

	$numeropg = 1;
	
	if(isset($_GET[ $get ]) )
	{
		$numeropg = abs((int)$_GET[ $get ] ); 
		
		if($numeropg == 0 ){ $numeropg = 1; }

		/*
			Si la page demandé est trop grande, 
			on lui donne le nombre maximal
		*/
		
		if($numeropg > $nbpg ){$numeropg = $nbpg;} 
	}
	
	/*
		Création des liens de paginations
	*/

	if(!empty($aget) )
	{
		$aget = '&amp;'.$aget;
	}

	$tablien = array();
	
	if($nbpg > 1 )
	{
		$continu = FALSE;
		$saute = $nbpg > 11;
		for($i = 1; $i <= $nbpg; $i++)
		{
			if($saute)
			{
				if( !( ( $i <= 3 ) 
					OR ($i <= $numeropg+2 
					AND $i >= $numeropg-2 )
					OR ($i > $nbpg -3 ) ) )
				{
					$continu = TRUE;
					continue;
				}
			}

			if($continu)
			{
				$continu = FALSE;
				$tablien[] = '...';
			}
			
			$lien = '<a href="'.$page.'?'.$get.'='.$i.$aget.'" >';
			
			if($i == $numeropg ){ $lien .= '<strong>'.$i.'</strong>'; }
			else{ $lien .= $i; }	
			
			$lien .= '</a>';

			$tablien[] = $lien;
		}
	}

	$liens = '<p class="pagin_lien" >'.implode($sep, $tablien).'</p>'; 

	/*
		Création de la LIMIT SQL
	*/

	$de = abs($nbaffiche * ($numeropg-1) );
	
	$limit = ' LIMIT '.$de.', '.$nbaffiche.' ';
	
	$retour = array($limit,$liens);
	
	return $retour;
}

/*
	Renvoi une ressource, modifi lien pour y mettre les liens de pagination
*/

function reqp($sql, &$lien, $nb_par_page=10, $pg='pg' )
{
	/*
		Cherchons le nombre d'entrées correspondant à la requête.
	*/

	$req_count = req('SELECT COUNT(*) AS nb_entre '.stristr($sql, 'FROM') );
	$nbent = $req_count->fetch(); 
	$nbent = (int)$nbent['nb_entre'];
	$nb_total = ($nb_par_page > 0 ) ? ceil($nbent/$nb_par_page) : 0 ; 
	
	/*
		Créons les liens de pagination. 
	*/
	$page = (isset($_GET[$pg]))?abs((int)$_GET[$pg]):0;
	
	if($nbent <= $nb_par_page )
	{
		$lien = '';
	}
	else
	{
		$c = $lien;
		$get = '';
		
		if($pos = strpos($c, '?') )
		{
			$get = '&amp;'.substr($c, $pos+1 );
			$c = substr($c, 0, $pos ); 
		}

		$lien = ''; 

		for($i=0;$i<$nb_total;$i++)
		{
			$class = ( $i == $page ) ? 'actif' : 'inactif'; 
			$lien.='<a class="'.$class.'" href="'.$c.'?pg='.$i.$get.'">'.($i+1).'</a> ';
		}

		$lien .='';
	}

	/*
		Créons la limit pour la requete
	*/

	$deb = $page * $nb_par_page;
	$limit = ' LIMIT '.$deb.','.$nb_par_page;

	return req($sql.$limit);
}

function lien($lien)
{
	$lien = trim($lien); 
	return empty($lien) ? '' : ( strpos($lien, 'http://' ) === 0 ? $lien : 'http://'.$lien ) ;
}

//URL cliquable fonction V4 (10/2005) Posté par Yves Maistriaux 
function lien_text($lien)
{
	$in=array(
		'`((?:https?|ftp)://\S+[[:alnum:]]/?)`si',
		'`((?<!//)(www\.\S+[[:alnum:]]/?))`si'
	);

	$out=array(
		'<a href="$1">$1</a>',
		'<a href="http://$1">$1</a>'
	);

	return preg_replace($in,$out,$lien);
}


class pagin
{
	private $nbp;
	private $url;
	private $actif; 

	function mut_nbp($nbp)
	{
		$this->nbp = absint($nbp); 
	}

	function mut_url($url)
	{
		if( $ret = (strpos($url, '%p' ) !== FALSE ) )
			$this->url = $url;
		return $ret;
	}

	function mut_actif($nb)
	{
		$this->actif = absint($nb); 
	}

	function affiche()
	{
		if( $this->nbp > 1 )
		{
			echo '<p class="pagin" >'; 
			for( $j=0; $j<$this->nbp; $j++ )
			{
				echo '<a href="', str_replace('%p', $j, $this->url ),'" ';
				if( $j == $this->actif )
					echo 'class="actif"'; 
				echo '>', $j+1, '</a> '; 
			}
			echo '</p>'; 
		}
	}
}

function checked($var)
{
	if( $var )
	{
		echo 'checked="checked"'; 
	}
}

function selected($var)
{
	if( $var )
	{
		echo 'selected="selected"'; 
	}
}

function mysubstr($chaine, $deb, $long )
{
	return utf8_decode( substr( utf8_encode($chaine), $deb, $long) );
}

function est_class($c, $n )
{
	return is_object($c) && get_class($c) == $n; 
}

// Null ou int 
function noui($i)
{
	return is_numeric($i) ? (int)$i : NULL; 
}

function auto_format($text)
{

	/*
		Extraction/remplacement des url/mail.
	*/

	$nb = preg_match_all("`\b(?:(?:(?:https?|ftp|file)://|www\.|ftp\.)[-A-Z0-9+&@#/%?=~_|$!:,.;]*[-A-Z0-9+&@#/%=~_|$]|((?:mailto:)?[A-Z0-9._%+-]+@[A-Z0-9._%-]+\.[A-Z]{2,4})\b)`i", $text, $matches); 
	$tab_url = $matches[0]; 

	for( $i=0; $i<$nb; $i++ )
	{
		$text = str_replace($tab_url[$i], '{{addr'.$i.'}}', $text); 
	}

        // Heur
        $tab_masque=array(
		'`(\d{1,2})\s?heure?s?(\s?\d{1,2})?`i', 
		'`(\d{1,2})\s?h(\s(\d{1,2}[^h€]))?`i',
		'`(?:de\s?)?(\d{1,2})\s?h(\s?\d{1,2})?\s?[à-]\s?(\d{1,2})\s?h(\s?\d{1,2})?`i',
		'`(\d{1,2})h00`i',
		'`(\d+)\s?(mn|minutes?)`i',
		// Tèl
		'`(\d{2})[. ,-]?(\d{2})[. ,-]?(\d{2})[. ,-]?(\d{2})[. ,-]?(\d{2})`i',
		// Ponctuation
		'`\s*(\.{1,3}|,) *`i',
		'`\s*([;!:?])\s*`i',
		// km, euro
		'`(\d+)\s*(kilomètres?|kms?)`i',
		'`euros?([.!?]|\s|$)`i',
		'`(\d+)\s*€`i',
		'`(\d+)\s*[,.]\s*(\d+)\s?(€|km)`i',
	);

	$tab_remp = array(	
		'$1h$2', 
		'$1h$3', 
		'$1h$2 à $3h$4', 
		'$1h', 
		'$1min',
		// Tèl
		'$1 $2 $3 $4 $5', 
		// Ponctuation	
		'$1 ', 
		' $1 ',
		// km, euro
		'$1km',
		'€$1',
		'$1€',
		'$1.$2$3',
	);

	$text = preg_replace($tab_masque, $tab_remp, $text); 

	$text = ucfirst($text); 
	$text = trim($text); 
	if( preg_match('`[^.!?]$`', $text) )
	{
		$text .= '.'; 
	}

	/*
		On remet les url/mail.
	*/

	for($i=0; $i<$nb; $i++)
	{
		$text = str_replace('{{addr'.$i.'}}', $tab_url[$i], $text); 
	}

	return $text; 
}

function spesubstr($chaine, $deb, $long )
{
	$chaine =substr($chaine, $deb, $long ); 
	$chaine = substr( $chaine , 0, strrpos($chaine, ' ', -1 ) ); 
	return $chaine; 
}

/*
	Proposé par : Pierre COUSTILLAS 
	Fonctions "myErrorHandler" et "debug" pour séparer les erreurs/débugage de l'affichage du site. 

	<fichier de log> ex : /var/tmp/php_errors.log. 

	Dans une console : tail -sF 1 <fichier de log>  

	php.ini : 
		log_errors = On 
		display_errors = Off 
		error_log = <fichier de log> 
*/ 

$ERREUR_PHP = FALSE; 

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
	global $ERREUR_PHP;

	$ERREUR_PHP = TRUE; 	

	$tab_err[E_ERROR]='E_ERROR';
	$tab_err[E_WARNING]='E_WARNING';
	$tab_err[E_PARSE]='E_PARSE';
	$tab_err[E_NOTICE]='E_NOTICE';
	$tab_err[E_CORE_ERROR]='E_CORE_ERROR';
	$tab_err[E_CORE_WARNING]='E_CORE_WARNING';
	$tab_err[E_COMPILE_ERROR]='E_COMPILE_ERROR';
	$tab_err[E_COMPILE_WARNING]='E_COMPILE_WARNING';
	$tab_err[E_USER_ERROR]='E_USER_ERROR';
	$tab_err[E_USER_WARNING]='E_USER_WARNING';
	$tab_err[E_USER_NOTICE]='E_USER_NOTICE';
	$tab_err[E_STRICT]='E_STRICT';
	$tab_err[E_RECOVERABLE_ERROR]='E_RECOVERABLE_ERROR';
	$tab_err[E_DEPRECATED]='E_DEPRECATED';
	$tab_err[E_USER_DEPRECATED]='E_USER_DEPRECATED';

	$msg_erreur = $tab_err[$errno].' '.$errstr.' in line '.$errline.' in file '.$errfile;
	debug($msg_erreur,TRUE);
	return true;
}

$old_error_handler = set_error_handler("myErrorHandler");

$TAB_LOG = array(); 

define('DEBUG_BACKTRACE_TRUE', TRUE);
define('DEBUG_BACKTRACE_FALSE', FALSE);
define('ERREUR_PHP_TRUE', TRUE); 
define('ERREUR_PHP_FALSE', FALSE); 


function debug($texte, $backtrace=FALSE, $erreur_php=FALSE)
{
	global $TAB_LOG, $ERREUR_PHP; 
	
	if( $erreur_php )
	{
		$ERREUR_PHP=TRUE; 
	}

	$msg = ''; 

	if ( $backtrace )
	{
		$tab_trace=debug_backtrace();
		$msg .= '<ul>'; 

		foreach ($tab_trace as $tab_trace_mini)
		{
			if (isset($tab_trace_mini['line']))
			{
				$msg .="<li>" . str_replace(' ', '&nbsp;', str_pad($tab_trace_mini['line'],4," ", STR_PAD_LEFT) ) 
					. " " . basename($tab_trace_mini['file']) . " ". $tab_trace_mini['function']."</li>";
			}
		}
		
		$msg .= '</ul>'; 
	}

	if( is_string($texte) )
	{
		$msg .= '<p>'.$texte.'</p>'; 
	}
	else
	{
		$var = var_dump_str($texte); 
		$msg .= '<pre>'.$var.'</pre>'; 
	}

	$TAB_LOG[] = $msg;
}

function var_dump_str($str)
{
	ob_start();
	var_dump($str);
	$str = ob_get_contents();
	ob_end_clean();
	return $str; 
}

function tps()
{
	static $actif=FALSE, $tps; 

	if( $actif )
	{
		$actif = FALSE; 
		return round( ( microtime(TRUE)-$tps ) *1000, 2);
	}
	else
	{
		$tps = microtime(TRUE); 
		return ($actif = TRUE ); 
	}
}

function code_aleat($nb)
{
	$tmp = $nb; 
	if( $tmp % 2 > 0 )
	{
		$tmp++; 
	}

	$aleat = bin2hex(openssl_random_pseudo_bytes( $tmp/2 ) );

	if( strlen($aleat) > $nb )
	{
		$aleat = substr($aleat, 0, $nb); 
	}

	return $aleat; 
}
