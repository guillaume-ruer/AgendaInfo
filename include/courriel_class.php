<?php

class courriel extends objet 
{
	// Liste des destinataire 
	protected $dest=array(); 

	// Adresse de réponse 
	protected $retour='';

	// Expediteur
	protected $exp=''; 

	// Parti html du mail
	protected $html=''; 

	// Parti texte 
	protected $texte=''; 
	
	private $boundary=''; 
	protected $piece_jointe=array(); 

	// Sujet du mail 
	protected $sujet=''; 

	function mut_dest($dest)
	{
		if( is_array($dest) )
		{
			$this->dest = $dest; 
		}
		elseif( is_string($dest) )
		{
			$this->dest = explode(',', $dest); 
		}
	}

	function mut_piece_jointe($piece_jointe)
	{
		if( is_array($piece_jointe) )
		{
			$this->piece_jointe = array(); 

			foreach($piece_jointe as $pc )
			{
				if( file_exists($pc) )
				{
					$this->piece_jointe[] = $pc; 
				}
			}
		}
		else
		{
			if( file_exists($piece_jointe) )
			{
				$this->piece_jointe[] = $piece_jointe; 
			}
		}
	}

	function ajt_entete($entete)
	{
		$this->entete[] = $entete; 
	}

	private function message($boundary, $rc )
	{
		$bound = $rc.'--'.$boundary.$rc; 
		$message  = $bound; 
		$message .= 'Content-Type: text/plain; charset=UTF-8'.$rc; 
		$message .= 'Content-Transfer-Encoding: 8bit'.$rc; 
		$texte = empty($this->texte) ? strip_tags(str_replace('<br />', "\n", $this->html) ) : $this->texte; 
		$message .= $rc.$texte.$rc; 

		$message .= $bound; 
		
		// Html 
		$message .= 'Content-Type: text/html; charset=UTF-8'.$rc; 
		$message .= 'Content-Transfer-Encoding: 8bit'.$rc; 
		$message .= $rc.$this->html.$rc; 
		
		$message .= $rc.'--'.$boundary.'--'.$rc; 

		return $message; 
	}

	function envoie()
	{
		if( !($finfo = finfo_open(FILEINFO_MIME) ) ) 
		{   
			exit('Impossible d\'ouvrir la base de données.'); 
		} 

		foreach($this->dest as $dest )
		{
			// Saut de ligne 
			if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $dest))
			{
				$rc = "\r\n";
			}
			else
			{
				$rc = "\n";
			}

			if( empty($this->piece_jointe) )
			{
				$content_type = 'multipart/alternative'; 
				$pc = FALSE; 
			}
			else
			{
				$content_type = 'multipart/mixed'; 
				$boundary_alt = "-----=".md5(rand()); 
				$pc = TRUE; 
			}
			
			// Entête 
			$boundary = "-----=".md5(rand());

			$entete = 'From: '.$this->exp.$rc; 
			$entete .= 'Reply-to: '.$this->retour.$rc; 
			$entete .= 'MIME-version: 1.0'.$rc; 
			$entete .= 'Content-Type: '.$content_type.';'.$rc; 
			$entete .= ' boundary="'.$boundary.'"'.$rc; 

			// Texte 
			$message = ''; 

			if($pc)
			{
				$message  = $rc."--".$boundary.$rc;
				$message .= "Content-Type: multipart/alternative;".$rc;
				$message .= " boundary=\"$boundary_alt\"".$rc;
				$message .= $this->message($boundary_alt, $rc);

				foreach($this->piece_jointe as $piece_jointe )
				{
					$message .= $rc."--".$boundary.$rc; 

					$fichier   = fopen($piece_jointe, "r");
					$attachement = fread($fichier, filesize($piece_jointe));
					$attachement = chunk_split(base64_encode($attachement));
					fclose($fichier);

					$mime = finfo_file($finfo, $piece_jointe); 

					$message.= 'Content-Type: '.$mime.'; name="'.basename($piece_jointe).'"'.$rc;
					$message.= 'Content-Transfer-Encoding: base64'.$rc;
					$message.= 'Content-Disposition: attachment; filename="'.basename($piece_jointe).'"'.$rc;
					$message.= $rc.$attachement.$rc.$rc;
				}
			}
			else
			{
				$message = $this->message($boundary, $rc); 
			}

			$message .= $rc.'--'.$boundary.'--'.$rc; 
			
			$debug =  '<table>'; 
			$debug .=  '<tr>';
			$debug .=  '<th>Entête</th>'; 
			$debug .=  '<td><pre>'.htmlentities($entete).'</pre></td>'; 
			$debug .=  '</tr>'; 
			$debug .=  '<tr>';
			$debug .=  '<th>Dest</th>'; 
			$debug .=  '<td><pre>'.htmlentities($dest).'</pre></td>'; 
			$debug .=  '</tr>'; 
			$debug .=  '<tr>';
			$debug .=  '<th>Message</th>'; 
			$debug .=  '<td><pre>'.htmlentities($message).'</pre></td>'; 
			$debug .=  '</tr>'; 
			$debug .=  '</table>'; 

			debug($debug); 

			mail($dest, $this->sujet, $message, $entete); 
		}
	}

}
