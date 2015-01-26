<?php
/**
 * Utilisations de pipelines par Réservations multiples
 *
 * @plugin     Réservations multiples
 * @copyright  2014
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Reservations_multiples\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	

function reservations_multiples_formulaire_charger($flux){
	$form = $flux['args']['form'];
	if ($form=='reservation'){
		$champs_extras_auteurs_add=array();	
		$ajouter=array();						
		$nombre_auteurs=intval(_request('nr_auteurs'))?_request('nr_auteurs'):(_request('nombre_auteurs')?_request('nombre_auteurs'):'');
		if(_request('nr_auteurs')=='nada')$nombre_auteurs=0;
		$i = 1;
		while ($i <= $nombre_auteurs) {
			$nr=$i++;
		    $ajouter[$nr]=$nr;
			$flux['data']['nom_'.$nr]='';
			$flux['data']['email_'.$nr]='';	
			if($flux['data']['champs_extras_auteurs']){
				//Adapter les champs extras
		       foreach($flux['data']['champs_extras_auteurs'] as $key =>$value){
		           $flux['data'][$value['options']['nom'].'_'.$nr]='';
				   $champs_extras_auteurs_add[$nr][$key]=$value;  
		           $champs_extras_auteurs_add[$nr][$key]['options']['nom']=$value['options']['nom'].'_'.$nr;              
		            }
		        }	
			}
		$flux['data']['nombre_auteurs']=$nombre_auteurs;
		$flux['data']['nr_auteurs']='';
		$flux['data']['champs_extras_auteurs_add']=$champs_extras_auteurs_add;
		$flux['data']['ajouter']=$ajouter;
		$flux['data']['_hidden'].='<input type="hidden" name="nombre_auteurs" value="'.$flux['data']['nombre_auteurs'].'">';

		}
			
	return $flux;
}

function reservations_multiples_formulaire_verifier($flux){
	$form = $flux['args']['form'];
	if ($form=='reservation'){
		//enlever le message d'erreur en attendand de comnprendre d'ou vient ce message qui se met d'office
		unset($flux['data']['message_erreur']);		
		
		//Une erreur bidon pour éviter ne pas traiter le formulaire lors de modification de nombre de inscrits
		if(_request('nr_auteurs')){				
			$flux['data']=array(
				'ajouter' => 'ajouter auteurs',
				);
			}
		elseif($nombre=_request('nombre_auteurs')){				
			include_spip('inc/saisies');
			include_spip('cextras_pipelines');
			$erreurs=array();

			if(function_exists('champs_extras_objet')){
				$champs_extras_auteurs=champs_extras_objet(table_objet_sql('auteur'));
										
				 //Stocker les valeurs intitiales des champs extras
				foreach($champs_extras_auteurs as $key =>$value){
					$$value['options']['nom']=_request($value['options']['nom']);
				}
			}
			else $champs_extras_auteurs=array();
			
			//Vérification des champs additionnels
			$i = 1;
			while ($i <= $nombre) {
				$nr=$i++;
				
				//les champs de bases obligatoires	
				$obligatoires=array('nom_'.$nr,'email_'.$nr);	
				
				//Tester les champs de bases obligatoires
				foreach($obligatoires AS $champ){
	            	if(!_request($champ))$erreurs[$champ]=_T("info_obligatoire");
	        		}	

		         if ($email=_request('email_'.$nr)){
		            include_spip('inc/filtres');
		            // la validité du mail
		            if (!email_valide($email)){
		                $erreurs['email_'.$nr] = _T('form_prop_indiquer_email');
		                }
		            }
				
        		//Vérifier les champs extras
				foreach($champs_extras_auteurs as $key =>$value){
					
					// Adapter les request pour pouvoir faire la vérification des champs extras						
					set_request($value['options']['nom'],_request($value['options']['nom'].'_'.$nr));
					$e=saisies_verifier($champs_extras_auteurs);
					
					//Adapter le nom du champ
					if(is_array($e)){
						foreach($e AS $champ=>$erreur){
							$erreurs[$champ.'_'.$nr]=$erreur;
							}
						}
					}					
				}

    		//Remettre les valeurs initiales
			foreach($champs_extras_auteurs as $key =>$value){
				set_request($value['options']['nom'],$$value['options']['nom']);
				}
				$flux['data']=array_merge($flux['data'],$erreurs);
				
			//remettre 	le message d'erreur
			if (count($flux['data'])>0) $flux['data']['message_erreur'] = _T('reservation:message_erreur');					
			}			
		}

	return $flux;
}

function reservations_multiples_formulaire_traiter($flux){
	$form = $flux['args']['form'];
	if ($form=='reservation' AND $nombre=_request('nombre_auteurs')){
		$noms=array(_request('nom'));
		//Enregistrement des champs additionnels
		$enregistrer=charger_fonction('reservation_enregistrer','inc');
		
		//Lister les messages de retour
		preg_match('/<h3>(.*?)<\/h3>/s',$flux['data']['message_ok'], $match);		
		$titre=$match[0];
					
		preg_match('/<p(.*?)<\/p>/s',$flux['data']['message_ok'], $match);		
		$intro=$match[0];		
		preg_match('/<table(.*?)<\/table>/s',$flux['data']['message_ok'], $match);  
			
		$message_ok=array($match[0]);
			
		
		if(function_exists('champs_extras_objet')){
			$champs_extras_auteurs=champs_extras_objet(table_objet_sql('auteur'));
		}
		else $champs_extras_auteurs=array();
		// ne pas créer de compte spip
		set_request('enregistrer','');	
		$i = 1;
		
		//inscription aux mailinglistes
		if(test_plugin_actif('reservations_mailsubscribers')){
			$inscription=charger_fonction('inscription_mailinglinglistes','inc');		
		}
		
		while ($i <= $nombre) {
			//recupérer les champs par défaut
			
			$nr=$i++;
			$email=_request('email_'.$nr);
			set_request('nom',_request('nom_'.$nr));
			set_request('email',$email);		
			$noms[]	= _request('nom');
    		//Vérifier les champs extras
			foreach($champs_extras_auteurs as $key =>$value){
									
				// récupérer les champs extras					
				set_request($value['options']['nom'],_request($value['options']['nom'].'_'.$nr));
				}
			set_request('nr_auteur',$nr);
			
			//Enregistrer				
			$flux['data']=$enregistrer('','','',$champs_extras_auteurs);
			preg_match('/<table(.*?)<\/table>/s',$flux['data']['message_ok'], $match);  
			$message_ok[]=$match['0'];
			$nr=0;
			
			//inscription aux mailinglistes
			if(test_plugin_actif('reservations_mailsubscribers')){
				$inscription($email);	
				}

			
		}
			//Recopiler le messages de retour
			$m=$intro;
			$m.=$titre;			
			foreach($message_ok AS $message){
				$m.="<h4>$noms[$nr]</h4>";				
				$m.=$message;				
				$nr++;
			}		
			$flux['data']['message_ok']=$m;
	}
	return $flux;
}

function reservations_multiples_recuperer_fond($flux){
	$fond = $flux['args']['fond'];
	if ($fond=='formulaires/inc-reservation_connection'){
		$auteurs_multiples=recuperer_fond('inclure/auteurs_multiples',$flux['data']['contexte'],array('ajax'=>'oui'));
		$flux['data']['texte'] .=  $auteurs_multiples;
		}
	return $flux;
}