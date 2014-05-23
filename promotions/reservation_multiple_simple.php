<?php


if (!defined("_ECRIRE_INC_VERSION")) return;       
           
function promotions_reservation_multiple_simple_dist($flux=array(),$option=''){
	
	$return=array(
		'nom'=>_T('reservations_multiples:nom_reservation_multiple_simple'),
		'saisies'=>	 array(
						array(
							'saisie' => 'input',
							'options' => array(
								'nom' => 'reduction',
								'label' => _T('reservations_multiples:label_reduction'),
								'obligatoire'=>'oui'
							)
						),
						array(
							'saisie' => 'selection',
							'options' => array(
								'nom' => 'type_reduction',
								'label' => _T('reservations_multiples:label_type_reduction'),
								'datas'=>array(
									'pourcentage'=>_T('reservations_multiples:pourcentage'),
									'absolu'=>_T('reservations_multiples:absolu')							
									), 
							'obligatoire'=>'oui'							
							)
						),
					)	
			);
	
	if($option AND isset($return[$option]))	$return=$return[$option];
						

    return $return;
}

function promotions_reservation_multiple_simple_action_dist($flux,$promotion){
		
	$prix_original=$flux['data']['prix_ht'];
	$reduction=$promotion['valeurs_promotion']['reduction'];
	$type_reduction=$promotion['valeurs_promotion']['type_reduction'];
	
	$nr_auteur=_request('nr_auteur');
	$nombre_auteurs=_request('nombre_auteurs');
	
	if($nombre_auteurs and !$nr_auteur){
			if($type_reduction=='pourcentage')$flux['data']['prix_ht']=$prix_original-($prix_original/100*$reduction);
			elseif($type_reduction=='absolu')$flux['data']['prix_ht']=$prix_original-$reduction;
	}

	//if($id_objet=$flux['data']['id_reservation_detail']) sql_insertq('spip_promotions_liens');

    return $flux;
}


?>