<?php
class article
{
	public $table = 'article';
	public $table_commentaire = 'article_commentaire'; 
	public $conf=array();

	public $id=0;
	private $date=0;
	private $etat=0;
	private $type=0; 
	public $titre='';
	public $article='';

	private $id_createur=0;
	private $createur='';

	public $commentaire=TRUE;//Indique si l'article est ouvert en commentaire
	private $tab_commentaire; 
	public $c_page=NULL;
	public $c_nbparpage=NULL; 
	public $c_nbpage=0; 
	
	function __construct($conf )
	{
		$this->conf = $conf; 
	}

	function mut_etat($etat, $droit )
	{
		if(isset($this->conf[$this->type]['etat'][$etat]) )
		{
			if( article_verif_droit(article_droit_etat($this->conf,$this->type, $etat), $droit )  )
			{
				$this->etat = $etat; 
				return TRUE;
			}
			else
			{
				return FALSE; 
			}
		}
		else
		{
			return FALSE;
		}
	}

	function acc_etat($num=FALSE)
	{
		return $num ? $this->etat : $this->conf[$this->type]['etat'][$this->etat]; 	
	}

	function mut_type($type)
	{
		if(isset($this->conf[$type]) )
		{
			$this->type = $type;
			return TRUE; 
		}
		else
		{
			return FALSE; 
		}
	}

	function acc_commentaire($i)
	{
		return isset($this->tab_commentaire[$i]) ? $this->tab_commentaire[$i] : FALSE; 
	}

	function acc_id_createur()
	{
		return $this->id_createur; 
	}

	function sup_tab_commentaire($i)
	{
		if( isset($this->tab_commentaire[$i]) )
		{
			unset($this->tab_commentaire[$i] );
			return TRUE; 
		}
		else
		{
			return FALSE; 
		}
	}

	function mut_id_createur($id_createur)
	{
		if($this->id_createur==0)
		{
			$this->id_createur=$id_createur;
			return TRUE;
		}
		else
		{
			return FALSE; 
		}
	}

	function acc_type($num=FALSE)
	{
		return $num ? $this->type : $this->conf[$this->type]['nom']; 
	}

	function acc_date()
	{
		return date('j/n/Y G\hi', $this->date); 
	}

	function acc_createur()
	{
		return secuhtml($this->createur); 
	}	

	
	function bdd($id)
	{
		$donne = req('SELECT a.id, a.id_createur, a.article, a.etat, a.commentaire, a.date, a.titre, a.type, u.User createur
			FROM article a
			LEFT JOIN Utilisateurs u
				ON a.id_createur = u.id
			WHERE a.id='.absint($id).'
		');

		if( $do = fetch($donne ) )
		{
			$this->id = absint($do['id']); 
			$this->id_createur = absint($do['id_createur']); 
			$this->createur = $do['createur']; 
			$this->article = $do['article'];
			$this->etat = absint($do['etat']); 
			$this->commentaire = (bool)$do['commentaire']; 
			$this->date = absint($do['date']); 
			$this->titre = $do['titre'];
			$this->type = absint($do['type']); 

			if($this->commentaire)
			{
				$donne = new reqa('SELECT ac.id, ac.id_utilisateur, ac.commentaire, u.User pseudo, ac.date
					FROM article_commentaire ac
					LEFT JOIN Utilisateurs u
						ON ac.id_utilisateur = u.id
					WHERE id_article = '.$this->id.'
					ORDER BY ac.date
				', NULL, $this->c_page, $this->c_nbparpage ); 
				$this->c_nbpage = $donne->nb_page; 

				while($do = $donne->parcours() )
				{
					$c = new commentaire; 
					$c->id = $do->id; 
					$c->commentaire = $do->commentaire; 
					$c->id_utilisateur = $do->id_utilisateur;
					$c->utilisateur = $do->pseudo;
					$c->date = $do->date;
					$this->tab_commentaire[] = $c; 
				}
			}
			return TRUE;
		}
		else
		{
			return FALSE; 
		}
	}
}

class gere_article extends article
{
	public function enr($membre_droit, $membre_id)
	{
		if(empty($this->id) )
		{
			if(article_verif_droit( article_droit_ajt($this->conf, $this->acc_type(TRUE) ), $membre_droit ) )
			{
				return $this->insert(); 
			}
			else
			{
				return FALSE; 
			}
		}
		else
		{
			if(article_membre_droit($this->conf, $this->acc_type(TRUE), $this->acc_id_createur(), $membre_id, $membre_droit) )
			{
				return $this->maj(); 
			}
			else
			{
				return FALSE; 
			}
		}
	}

	private function insert()
	{
		$donne = prereq('INSERT INTO article(type, etat, commentaire, article, id_createur, date, titre )
			VALUES(?,?,?,?,?, ?,? ) ');

		exereq($donne, array(
			$this->acc_type(TRUE), $this->acc_etat(TRUE), $this->commentaire, $this->article, $this->acc_id_createur(), time(), $this->titre
		) ); 
		$this->id = derid(); 
		return TRUE; 
	}

	private function maj()
	{
		$donne = prereq('
			UPDATE article SET type=?, etat=?, commentaire=?, article=?, titre=?
			WHERE id=? LIMIT 1 
		');

		exereq($donne, array(
			$this->acc_type(TRUE), $this->acc_etat(TRUE), $this->commentaire, $this->article, $this->titre, $this->id
		) ); 
		return TRUE; 
	}

	function sup($id)
	{
		req('DELETE FROM article WHERE id='.absint($id).' LIMIT 1 ');
		req('DELETE FROM article_commentaire WHERE id_article='.absint($id).' '); 
	}

	function sup_commentaire($id, $membre )
	{
		// Soit on a le droit de gerer les commentaires de l'article 
		if( $this->conf[$this->acc_type(TRUE)]['droit']['com'] & $membre->droit )
		{
			gere_commentaire::sup($id); 
			return TRUE;
		}
		else
		{
			// soit on le membre à crée le commentaire 
			req('DELETE FROM article_commentaire WHERE id='.absint($id).' AND id_utilisateur='.absint($membre->id).' LIMIT 1 '); 
			return TRUE;
		}

		for( $i=0; $c = $this->acc_commentaire($i); $i++ )
		{
			if( $c->id == $id )
			{
				$this->sup_tab_commentaire($i); 
				break; 
			}
		}
	}
}

class commentaire
{
	public $id; 
	public $id_article;
	public $commentaire;
	public $date; 

	public $id_utilisateur;
	public $utilisateur;

	function acc_id()
	{
		return absint($this->id);
	}

	function acc_id_article()
	{
		return absint($this->id_article);
	}

	function acc_commentaire()
	{
		return secuhtml($this->commentaire); 
	}

	function acc_date()
	{
		return date('j/n/Y G\hi', $this->date); 
	}

	function acc_id_utilisateur()
	{
		return absint($this->id_utilisateur);
	}

	function acc_utilisateur()
	{
		return secuhtml($utilisateur); 
	}
}

class gere_commentaire extends commentaire
{

	function insert()
	{
		$donne = prereq('INSERT INTO article_commentaire(id_article, id_utilisateur, commentaire, date)
			VALUES(?,?,?,?)');
		exereq($donne, array($this->id_article, $this->id_utilisateur, $this->commentaire, time() ) ); 
		$this->id = derid(); 
	}

	function maj()
	{
		$donne = prereq('UPDATE article_commentaire SET id_article=?, id_utilisateur=?, commentaire=?
			WHERE id=? ');
		exereq($donne, array($this->id_article, $this->id_utilisateur, $this->commentaire, $this->id ) ); 
	}

	function sup($id)
	{
		req('DELETE FROM article_commentaire WHERE id='.absint($id).' LIMIT 1 '); 
	}

}

class ls_article
{
	private $type=0; 
	public $conf; 

	public $pagin=TRUE; 
	public $page=0; 
	public $nbparpage=10;

	function __construct($conf) 
	{
		$this->conf = $conf; 
	}
	
	function mut_type($type)
	{
		if(isset($this->conf[$type]) )
		{
			$this->type = $type;			
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function req()
	{
		$where = $limit = ''; 

		if(!is_null($this->type) )
		{
			$where .= ' type='.$this->type.' ';
		}

		if( !empty($where) )
		{
			$where = ' WHERE '.$where;
		}

		if( !$this->pagin )
		{
			$limit = ' LIMIT '.$this->nbparpage; 	
			$this->page =NULL; 
		}

		$donne = new reqa('
			SELECT absint::a.id, secuhtml::a.titre, article_lien::a.article, absint::a.commentaire, secuhtml::u.User pseudo,
				absint::a.etat, absint::a.type, absint::a.id_createur, madate::a.date,
				COUNT( DISTINCT ac.id ) nbc
			FROM article a
			LEFT JOIN article_commentaire ac
				ON ac.id_article = a.id 
			LEFT JOIN Utilisateurs u 
				ON u.id = a.id_createur
			'.$where.'
			GROUP BY a.id 
			ORDER BY a.date DESC 
			'.$limit.'
		', NULL, $this->page, $this->nbparpage ); 

		return $donne; 
	}
}

function article_lien($article)
{
	return lien_text( secuhtml($article) ); 
}
