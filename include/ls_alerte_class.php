<?php

class ls_alerte extends reqo 
{
	private $idevent = NULL; 
	private $type = NULL; 
	private $source = NULL; 

	function __construct($do=array() )
	{
		$this->mut_sorti('alerte'); 

		parent::__construct($do); 
	}

	function mut_idevent($id){ $this->idevent=$id; }
	function acc_idevent(){ return $this->idevent; } 
	function mut_type($t){ $this->type = $t; }
	function acc_type(){ return $this->type; } 
	function mut_source($s){ $this->source = $s; }


	function requete($req=NULL)
	{
		$sql = '
			SELECT a.type, a.time, a.cause, a.idevent, a.id, ed.Titre titre
			FROM alerte a
			LEFT JOIN Evenement_details ed
				ON ed.Evenement_id = a.idevent
			LEFT OUTER JOIN Evenement e 
				ON e.id = a.idevent
			WHERE a.etat='.NON_VERIFIER.' 
			'.( is_null($this->type) ? '' : 'AND a.type='.absint($this->type) ).' 
			'.( is_null($this->idevent) ? '' : 'AND a.idevent='.$this->idevent) .'
			'.( is_null($this->source) ? '' : 'AND e.source='.$this->source) .'
			ORDER BY time DESC
		'; 
		parent::requete($sql); 
	}
}
