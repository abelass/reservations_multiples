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

		$i = 1;
		while ($i <= $nombre_auteurs) {
			$nr=$i++;
		    $ajouter[$nr]=$nr;
			$flux['data']['nom_'.$nr]='';
			$flux['data']['email_'.$nr]='';	
			if($flux['data']['champs_extras_auteurs']){
				//Adapter les champs extras
		       foreach($flux['data']['champs_extras_auteurs'] as $key =>$value){
		           $valeurs[$value['options']['nom'].'_'.$nr]='';
				   $champs_extras_auteurs_add[$nr][$key]=$value;  
		           $champs_extras_auteurs_add[$nr][$key]['options']['nom']=$value['options']['nom'].'_'.$nr;              
		            }
		        }	
			}
		$flux['data']['nombre_auteurs']=$nombre_auteurs;
		$flux['data']['nr_auteurs']='';
		$flux['data']['champs_extras_auteurs_add']=$champs_extras_auteurs_add;
		$flux['data']['ajouter']=$ajouter;
		$flux['data']['_hidden']='<input type="hidden" name="nombre_auteurs" value="'.$flux['data']['nombre_auteurs'].'">';
		//echo _request('nr_auteurs');
		}
			
	return $flux;
}

function reservations_multiples_formulaire_verifier($flux){
	$form = $flux['args']['form'];
	if ($form=='reservation'){
		
		//Une erreur bidon pour éviter ne pas traiter le formulaire lors de modification de nombre de inscrits
		if(_request('nr_auteurs')){				
			$flux['data']=array(
				'ajouter' => 'ajouter auteurs',
				'message_erreur'=>''
				);
			}
		elseif($nombre=_request('nombre_auteurs')){				
			include_spip('inc/saisies');
			include_spip('cextras_pipelines');
			$champs_extras_auteurs=champs_extras_objet(table_objet_sql('auteur'));
			$obligatoires=array();	
									
			 //Stocker les valeurs intitiales des champs extras
			foreach($champs_extras_auteurs as $key =>$value){
				$$value['options']['nom']=_request($value['options']['nom']);
			}
			
			//Vérification des champs aditionnels
			$i = 1;
			while ($i <= $nombre) {
				$nr=$i++;
				
				//les champs de bases obligatoires	
				$obligatoires[]='nom_'.$nr;
				$obligatoires[]='email_'.$nr;
				
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
			
			//Tester les champs de bases obligatoires
			foreach($obligatoires AS $champ){
            	if(!_request($champ))$erreurs[$champ]=_T("info_obligatoire");
        		}	

    		//Remettre les valeurs initiales
			foreach($champs_extras_auteurs as $key =>$value){
				set_request($value['options']['nom'],$$value['options']['nom']);
				}
				$flux['data']=array_merge($flux['data'],$erreurs);							
			}			
		}
	
	return $flux;
}

function reservations_multiples_formulaire_traiter($flux){
	$form = $flux['args']['form'];
	if ($form=='reservation'){

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