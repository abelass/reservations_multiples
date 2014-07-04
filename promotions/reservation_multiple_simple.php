<?php


if (!defined("_ECRIRE_INC_VERSION")) return; 
      
// Définition des champs pour le détail du formulaire promotion du plugin promotions (https://github.com/abelass/promotions)          
function promotions_reservation_multiple_simple_dist($flux=''){
    return array('nom'=>_T('reservations_multiples:nom_reservation_multiple_simple'));
}

// Définition de l'action de la promotion  
function promotions_reservation_multiple_simple_action_dist($flux,$promotion=array()){
		
	$prix_original=isset($flux['data']['prix_original'])?$flux['data']['prix_original']:'';
	
	$prix_base=isset($flux['data']['prix_base'])?$flux['data']['prix_base']:$prix_original;

	$reduction=$flux['data']['reduction'];
	$type_reduction=$flux['data']['type_reduction'];
	
	$nr_auteur=_request('nr_auteur');
	$nombre_auteurs=_request('nombre_auteurs');
	
	//Si on est en présence de la première réservation d'une réservation multiple 
	if($nombre_auteurs and !$nr_auteur){
		
			//On applique les réductions prévues
			if($type_reduction=='pourcentage')$flux['data']['prix_ht']=$prix_original-($prix_base/100*$reduction);
			elseif($type_reduction=='absolu')$flux['data']['prix_ht']=$prix_original-$reduction;
	}
		$flux['data']['objet']='reservations_detail';
		$flux['data']['table']='spip_reservations_details';			

    return $flux;
}


?>