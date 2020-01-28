<?php

class remarquable extends objet
{
	protected $id=0;
	protected $titre = ''; 
	protected $type = 0; 

	protected $lat = 0.0; 
	protected $long = 0.0; 

	public static $m_long = [
		'crud_nom' => 'lng'
	];
	protected $desc = ''; 
	public static $m_desc = [
		'crud_nom' => 'texte'
	];

	protected $ville = '__ville'; 
	public static $m_ville = [
		'crud' => CRUD_IM | CRUD_INSNSV
	]; 

	protected $contact_titre=''; 
	protected $site = ''; 
	protected $tel = ''; 
	protected $adr = ''; 
	protected $mail = ''; 
	protected $contact = '__contact'; 
	public static $m_contact = [
		'crud' => CRUD_IM | CRUD_INSNSV
	]; 

	const MUSEE = 0; 
	const SITE = 1; 
	const CACHE = 2; 

	static public $TAB_TYPE = [ 
		self::MUSEE => ['nom' => 'Musée', 'img' => 'agenda_info_limousin_icone_musee.png'],
		self::SITE => ['nom' => 'Site remarquable', 'img' => 'agenda_info_limousin_icone_site.png'],
		self::CACHE => ['nom' => 'Géocaching', 'img' => 'agenda_info_limousin_icone_geocaching.png']
	];

	function aff_type()
	{
		echo self::$TAB_TYPE[$this->type]['nom']; 
	}

	public static function type_html($id, $width=NULL, $height=NULL)
	{
		$attr = []; 

		if( !is_null($width) )
		{
			$attr[] = 'width="'.$width.'"'; 
		}

		if( !is_null($height) )
		{
			$attr[] = 'height="'.$height.'"'; 
		}

		return '<img src="'.C_IMG.'groupe-remarquable/'.self::$TAB_TYPE[$id]['img'].'" title="'.self::$TAB_TYPE[$id]['nom'].'" '.implode(' ', $attr).' />'; 
	}

	function html()
	{
		$titre = secuhtml($this->titre); 
		$desc = secuhtml($this->desc); 
		$img = self::type_html($this->type); 

		if( $this->contact()->acc_id() == 0 )
		{
			$contact = ''; 
			if( !empty($this->tel) )
			{
				$contact .= $this->tel; 
			}

			if( !empty($this->site) )
			{
				$contact .= ' ['.lien_text($this->site).'] '; 
			}

			if( !empty($this->mail) )
			{
				$contact .= $this->mail; 
			}
		}
		else
		{
			$contact = $this->contact()->acc_titre().' '.$this->contact()->acc_tel().' '.lien_text($this->acc_site() ); 
		}

		$html = <<<START
		
		<div class="remarquable" >
			<div class="rem-col-gauche" >
				$img
			</div>
			<div class="rem-centre" >
				<h2>$titre</h2>

				<div class="rem-desc" >$desc</div>

				<div class="rem-contact" >$contact</div>
			</div>
		</div>
START;

		return $html; 
	}
}

