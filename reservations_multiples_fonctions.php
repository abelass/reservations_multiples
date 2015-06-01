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

if (!defined('_ECRIRE_INC_VERSION'))
  return;

/**
 * Charge les valeurs d'un formulaire
 *
 * @pipeline formulaire_charger
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 **/
function reservations_multiples_config_charger() {
  include_spip('inc/config');
  $config = lire_config('reservations_multiples',array());

  // Si pas de config on met les défauts
  if (count($config) == 0) {
    $config = array('multiple_personnes' => 'on');
  }

  return $config;
}
