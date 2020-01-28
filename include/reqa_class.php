<?php
/*
	Requête avancé 
		Utile pour listé les données lors d'une requête simple. 	
		Le tableau de rêgle sert à associé une fonction à utilisé sur un champs. 
		Les fonction peuvent être associé directement dans la requete en utilisant le :: 
		<nom_fonction>::<champ> [AS <alias>]
		Une pagination peut être demandé. 
	
	** propriété **
	$switch : vaut successivement TRUE et FALSE à chaque appelle de la méthode parcours(). 
	$nb_page : si la pagination est active, contiendra le nombre de page total. à utilisé pour crée les liens de paginations. 
	$objet_sorti : contient le nom de l'objet qui representera la ligne de résultat.
*/

class reqa
{
	private $donne;
	private $regle; 
	public $switch = FALSE; 
	public $num = 0; 
	public $nb_page = 0; 
	public $objet_sorti = 'parcours'; 
	public $tps = 0;
	public $pagin=NULL; 

	/*
		__construct : string $requete [, array $tabregle=array() [, int $numpage=NULL [ , int $nbparpage=10 ] ] ] 

		$requete : une requete sql 
		$tabregle : ce tableau sera transmis au constructeur de l'objet represantant la ligne retourné par la méthode parcours
		$numpage : le numéro de page, si ne vaut pas NULL, active la pagination.
		$nbparpage : le nombre de résultat à retourné par page, si la pagination est active. 
	*/

	function __construct($req, $regle=array(), $p=NULL, $nb_par_page=10, $url='' )
	{
		$tps = microtime(TRUE);

		$this->regle = array((array)$regle);

		if( strpos($req, '::' ) )
		{
			$req = $this->cree_tab_regle($req);
		}

		if(!is_null($p) )
		{
			$p = absint($p);
			$this->num = $p; 
			$nb_par_page = absint($nb_par_page ); 
			$nb_entre = nb_entre($req); 
			$req .= ' LIMIT '.($p * $nb_par_page ).', '.$nb_par_page; 
			$this->nb_page = ceil( $nb_entre / $nb_par_page ); 
			if( !empty($url) )
			{
				$this->pagin($url); 
			}
		}

		$this->donne = req($req); 
		$this->tps = round( ( microtime(TRUE)-$tps) *1000, 2 ); 
	}

	/*
		parcours() 
		retourne l'objet représantant la ligne de résultat, en passant le tableau de rêgle au constructeur. 
	*/

	public function parcours()
	{
		$this->switch = !$this->switch; 
		$this->num++; 
		return $this->donne->fetchObject($this->objet_sorti, $this->regle ); 
	}

	/*
		
	*/

	private function cree_tab_regle($sql)
	{
		$tab_regle = array(); 
		$ls_champ = entre2mot('select', 'from', $sql); 
		$tab = explode(',', $ls_champ);
		$nsql = 'SELECT ';

		foreach($tab as $champ )
		{
			$ch = $champ; 
			if( strpos($champ, '::') !== FALSE )
			{
				list($regle, $champ) =explode('::', $champ );
				$ch = $champ; 

				if(strpos($champ, 'AS') !== FALSE )
				{
					list( ,$champ ) = explode('AS', $champ );
				}
				elseif(strpos(trim($champ), ' ') !== FALSE )
				{
					list( ,$champ )= explode(' ', $champ );
				}

				if(strpos($champ, '.') !== FALSE )
				{
					list(,$champ) = explode('.', $champ );
				}
				
				$tab_regle[ trim($champ) ] = trim($regle); 
			}
				
			$nsql .= trim($ch).', ';
		}

		$this->regle = array( array_merge( $this->regle[0], $tab_regle ) ); 
		return rtrim( $nsql,', ')."\n".stristr($sql, 'FROM'); 
	}

	function pagin($url)
	{
		$pagin = new pagin; 
		$pagin->mut_url($url); 
		$pagin->mut_actif($this->num); 
		$pagin->mut_nbp($this->nb_page);
		$this->pagin = $pagin; 
	}
}

/*
	L'objet par défaut retourné par reqa::parcours 
	le constructeur traite les champs avec les donnée du tableau de rêgle. 
*/

class parcours
{
	public function __construct($regle = array() )
	{
		foreach($regle as $nom => $val )
		{
			$this->$nom = $val($this->$nom); 
		}
	}
}

