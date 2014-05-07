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
	$flux['data']['auteurs']='';
		
	}
	return $flux;
}

function reservations_multiples_formulaire_verifier($flux){
	$form = $flux['args']['form'];
	if ($form=='reservation'){

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
	echo $fond;
	if ($fond=='formulaires/inc-reservation_connection'){
		$auteurs_multiples=recuperer_fond('inclure/auteurs_multiples',$flux['data']['contexte'],array('ajax'=>'oui'));
		$flux['data']['texte'] = str_replace('<!--extra_connection2-->', '<!--extra_connection2-->1' . $auteurs_multiples , $flux['data']['texte']);
	}
	return $flux;
}
?>