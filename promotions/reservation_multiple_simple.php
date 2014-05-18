<?php


if (!defined("_ECRIRE_INC_VERSION")) return;       
           
function promotions_reservation_multiple_simple_dist($flux){

    
    return $flux;
}

function promotions_reservation_multiple_simple_action_dist($flux){
		
	$prix_original=$flux['data']['prix_ht'];
	$promotion=10;
	$type_reduction="pourcentage";
	
	$nr_auteur=_request('nr_auteur');
	$nombre_auteurs=_request('nombre_auteurs');
	
	if($nombre_auteurs and !$nr_auteur){
			if($type_reduction=='pourcentage')$flux['data']['prix_ht']=$prix_original-($prix_original/100*$promotion);
			elseif($type_reduction=='absolut')$flux['data']['prix_ht']=$prix_original-promotion;
	}

	//if($id_objet=$flux['data']['id_prix_objet']) sql_insertq('spip_promotions_liens');
    
    return $flux;
}


?>