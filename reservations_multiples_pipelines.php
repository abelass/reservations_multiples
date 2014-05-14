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
		$champs_extra=false;

			$champs_extra=true;
			$champs_extras_auteurs_add=array();

		$flux['data']['auteurs']='';				
		$nombre_auteurs=_request('nombre_auteurs')?_request('nombre_auteurs'):'';

		$ajouter=array();
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
				   $champs_extras_auteurs_add[$nr][$key]['options']['label']=extraire_multi($value['options']['label']);  
		           $champs_extras_auteurs_add[$nr][$key]['options']['nom']=$value['options']['nom'].'_'.$nr;              
		            }
		        }	
			}
		$flux['data']['nombre_auteurs']=$nombre_auteurs;
		$flux['data']['champs_extras_auteurs_add']=$champs_extras_auteurs_add;
		$flux['data']['ajouter']=$ajouter;

		}
			
	return $flux;
}

function reservations_multiples_formulaire_verifier($flux){
	$form = $flux['args']['form'];
	if ($form=='reservation'){
		if(_request('nombre_auteurs') OR _request('nombre_auteurs')==0)$flux['data']['ajouter'] = 'ajouter auteurs';
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
?>