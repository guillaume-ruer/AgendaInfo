<?php
// Option 
define('CRUD_FUSION', 0x1); // Les propriété de l'objet seront dans l'objet parent (héritage) ou contenant (pour une propriété contenant un objet)
define('CRUD_MAJ', 0x2);// Pour la mise à jour ( ne met pas à jour si cette option est absente 
define('CRUD_INS', 0x4); // Pour l'insertion (N'insert pas si cette option est absente)
define('CRUD_IGNORE', 0x8); // Par défaut on prend tout les champs d'un objet, ceci nous permet d'ignorer des champs inutile en bdd. 
define('CRUD_REC', 0x10); // Récursif
define('CRUD_TAB_CHAMP', 0x20); // Mettre un type tableau dans un champ (tableau sérializer). 
define('CRUD_ISV', 0x40); // Ignore Si Vide : n'ajoute pas le champ si la valeur est vide. 
define('CRUD_INSNSV', 0x40); // INSere Null Si Vide (pour les objets 

// Option composé
define('CRUD_IMR', CRUD_REC | CRUD_INS | CRUD_MAJ ); 
define('CRUD_IM', CRUD_INS | CRUD_MAJ ); 
define('CRUD_IMISV', CRUD_INS | CRUD_MAJ | CRUD_ISV); 
define('CRUD_IMTC', CRUD_INS | CRUD_MAJ | CRUD_TAB_CHAMP ); 
define('CRUD_IR', CRUD_REC | CRUD_INS ); 
define('CRUD_MR', CRUD_REC | CRUD_MAJ ); 

function crud_ppt($obj)
{
	$r = new ReflectionClass(get_class($obj) );  
	$tab_class = array(); 
	$tab_ppt = array(); 
	$act = $r; 

	// crée un tableau de propriété : tab[<nom class>] = array(); 
	while( $act !== FALSE )
	{
		$tab_class[] = $act->name; 
		$tab_ppt[$act->name] = array(); 
		$act = $act->getParentClass(); 
	}

	// Ajoute les propriété au tableau précendent : $tab_ppt[<nom class>] = array(<liste ppt>); 
	foreach($r->getProperties() as $p )
	{
		if( strpos($p->name, 'm_') === 0 ) 
		{   
			$class = $p->class; 
			$tab_ppt[ $class ][$p->name] = $class::${$p->name};
		}   
		elseif( !$p->isStatic() && $p->isProtected() )
		{   
			$tab_ppt[ $p->class ][$p->name] = $obj->{$p->name}();
		}   
	}

	// assemblage des propriétés si fusion au niveau de l'héritage
	for( $i=count($tab_ppt)-2; $i>= 0; $i-- )
	{
		$class = $tab_class[$i];
		$opt = isset($tab_ppt[ $tab_class[$i] ]['m_'.$class]['crud']) 
			? $tab_ppt[ $tab_class[$i] ]['m_'.$class]['crud']   
			: CRUD_FUSION;

		if( $opt & CRUD_FUSION )
		{   
			$tab_ppt[ $tab_class[$i] ] = array_merge($tab_ppt[ $tab_class[$i] ], $tab_ppt[ $tab_class[$i+1] ]); 
			unset($tab_ppt[ $tab_class[$i+1] ]); 
		}   
	}

	// Merge des propriété si fusion au niveau des champs 
	// Supprime les champs à ignorer 
	foreach( $tab_ppt as $class => $ppt )
	{
		foreach($ppt as $cle => $val )
		{
			if( isset( $ppt['m_'.$cle ]['crud'] ) )
			{
				$opt = $ppt['m_'.$cle ]['crud'];

				if( ($opt & CRUD_IGNORE) || ( ($opt & CRUD_ISV) && empty($val) ) )
				{
					unset($tab_ppt[$class][$cle]);
					unset($tab_ppt[$class]['m_'.$cle]); 
				}
				elseif( $opt & CRUD_FUSION )
				{
					$tp = crud_ppt( $val );
					reset($tp); 
					list($c, $v) = each($tp); 

					if( isset($ppt['m_'.$cle]['crud_fusion']) )
					{
						foreach($ppt['m_'.$cle]['crud_fusion'] as $cle_fusion => $val_fusion )
						{
							$v[$val_fusion] = $v[$cle_fusion];
							unset($v[$cle_fusion]);
						}
					}

					$tab_ppt[$class] = array_merge($tab_ppt[$class], $v ); 
					unset($tab_ppt[$class][$cle], $tab_ppt[$class]['m_'.$cle]); 
				}
			}
		}
	}

	return array_reverse($tab_ppt); 
}

function crud_enr($obj, $ch_opt=NULL)
{
	if ( is_null($obj) )
	{
		trigger_error('Tenter d\'insérer un objet NULL.'); 
		return FALSE; 
	}

	$tab_ppt = crud_ppt($obj); 
	$mode = $obj->id() == 0 ? CRUD_INS : CRUD_MAJ ; 

	// Parcours des class 
	foreach( $tab_ppt as $class => $ppt )
	{
		$tab_champ = array(); 	
		$tab_val = array(); 
		$tab_objet = array(); 

		// Parcours des propriété 
		foreach	( $ppt as $cle => $val )
		{
			// Option du champ 
			$opt = isset($ppt['m_'.$cle]['crud']) ? $ppt['m_'.$cle]['crud'] : CRUD_INS | CRUD_MAJ ; 

			if( ($opt & $mode) && ($cle != 'id') && (strpos($cle, 'm_')!==0) )
			{
				// Renomage du champ 
				$champ = isset($ppt['m_'.$cle]['crud_nom']) ? $ppt['m_'.$cle]['crud_nom'] : $cle; 

				if( is_object($val) )
				{
					// Traitement spécial pour les objet 
					if($opt & CRUD_REC )
					{
						crud_enr($val); 
					}

					$tab_champ[] = $champ; 

					if( $opt & CRUD_INSNSV && empty($val->acc_id() ) )
					{
						$tab_val[] = NULL; 
					}
					else
					{
						$tab_val[] = $val->acc_id(); 
					}
				}
				elseif( is_array($val) )
				{
					if( $opt & CRUD_TAB_CHAMP )
					{
						$tab_champ[] = $champ; 
						$tab_val[] = serialize($val); 
					}
					else
					{
						// Les traitements des objets dans les tableaux est reporté à la fin 
						$tab_objet[$cle] = array(); 
						$tab_objet[$cle] = $val; 
					}
				}
				else
				{
					$tab_champ[] = $champ; 
					$tab_val[] = $val; 
				}
			}
		}

		$table = crud_table($class); 

		switch($mode)
		{
			case CRUD_MAJ :
				
				if( !empty($tab_champ) )
				{
					$champ = implode(',', array_map(
						function($var){ return '`'.$var.'`=?'; }, 
						$tab_champ 
					)); 
					
					$tab_val[] = $obj->id(); 
					$req = "UPDATE `$table` SET $champ WHERE id=?"; 
					$pre = prereq($req); 
					exereq($pre, $tab_val); 
				}
			break; 
			case CRUD_INS : 
				$id = $obj->id() == 0 ? 'NULL' : $obj->id(); 

				$champ = empty($tab_champ) ? ''
					: ', '.implode(', ', array_map(
						function($var){ return '`'.$var.'`'; },
						$tab_champ
				)); 

				array_unshift($tab_val, $id); 
				$interogation = ( ($n=count($tab_val)-1) > 0) ? str_repeat(',?', $n) : ''; 

				$req = "INSERT INTO `$table` (id$champ)
					VALUES(?$interogation)";

				$pre = prereq($req);
				exereq($pre, $tab_val); 

				if( $obj->id() == 0 )
				{
					$obj->id = derid(); 
				}
			break; 
		}

		if( !is_null($ch_opt) )
		{
			$join = $ch_opt['join']; 
			$table = crud_table($join).'_'.crud_table($obj); 

			// Si INSERT échou pas grave 
			$tv = array( $join->id(),$obj->id() ); 
			$req2 = "\n".' INSERT INTO `'.$table.'` (id_'.crud_table($join).', id_'.crud_table($obj).')
				VALUES(?,?)'; 
			$pre = prereq($req2); 
			exereq($pre, $tv, SQL_IGNORE_ERREUR); 
		}

		foreach($tab_objet as $cle => $objet )
		{
			if( property_exists($class, 'm_'.$cle ) )
			{
				$ch = 'm_'.$cle; 
				$recurs = isset($class::${$ch}['crud']) && ($class::${$ch}['crud'] & CRUD_REC); 
				$o_class = substr($class::${$ch}['type'], 2); 
				$var = property_exists($o_class, 'id_'.$class); 
			}

			foreach( $objet as $o )
			{
				if( property_exists($o, 'id_'.$class) )
				{
					$o->{'id_'.$class} = $obj->id(); 

					if( $recurs )
					{
						crud_enr($o); 
					}
				}
				elseif( $recurs )
				{
					crud_enr($o, array('join' => $obj) ); 
				}
				else
				{
					$table = crud_table($class).'_'.crud_table($o); 

					// Si INSERT échou pas grave 
					$tv = array( $obj->id(), $o->id() ); 
					$req2 = "\n".' INSERT INTO `'.$table.'` (id_'.crud_table($class).', id_'.crud_table($o).')
						VALUES(?,?)'; 
					$pre = prereq($req2); 
					exereq($pre, $tv, SQL_IGNORE_ERREUR); 

				}

				$tab_id[] = $o->id(); 
			}

			if( $var)
			{ 
				$and = empty($tab_id) ? '' : ' and id NOT IN('.implode(',', $tab_id).')'; 
				req('DELETE FROM `'.crud_table($o_class).'` WHERE id_'.$class.'='.$obj->id(). ' '.$and );  
			}
			else
			{
				$and = empty($tab_id) ? '' : ' and id_'.$o_class.' NOT IN('.implode(',', $tab_id).')';
				$table = crud_table($class).'_'.crud_table($o_class); 
				req('DELETE FROM `'.$table.'` WHERE id_'.$class.'='.$obj->id().$and ); 
			}

		}
	}
}

function crud_table($obj)
{
	if( is_object($obj) )
	{
		$class = get_class($obj); 
	}
	else
	{
		$class = (string)$obj; 
	}

	$table = $class; 

	if( property_exists($class, 'm_'.$class) )
	{
		$prop = $class::${'m_'.$class}; 

		if( isset($prop['crud_table']) )
		{
			$table = $prop['crud_table']; 
		}
	}

	return $table; 
}

function meta_crud_nom($class)
{
	static $tab_crud_nom=array(); 

	if( !isset($tab_crud_nom[$class]) )
	{
		$tab_crud_nom[$class] = array(); 
		$r = new ReflectionClass($class); 

		foreach( $r->getProperties() as $p)
		{
			if( $p->isStatic() && (strpos($p->name, 'm_') === 0) )
			{
				$v = $class::${$p->name};
				if( is_array($v) && isset($v['crud_nom']) )
				{
					$tab_crud_nom[$class][ $v['crud_nom']] = substr($p->name, 2) ; 
				}
			}
		}
	}

	return $tab_crud_nom[$class]; 
}
