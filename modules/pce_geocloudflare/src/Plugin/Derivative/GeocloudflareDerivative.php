<?php

namespace Drupal\pce_geocloudflare\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Deriver for GeocloudflareCondition.
 *
 * Provides a deriver for
 * Drupal\pce_geocloudflare\Plugin\smart_content\Condition\GeocloudflareCondition.
 * Definitions are based on user's browser's cloudflare header value.
 */
class GeocloudflareDerivative extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [
      'country' => [
        'label' => 'Country Name',
        'type' => 'select',
        'options_callback' => [get_class($this), 'getCountryNameOptions'],
      ] + $base_plugin_definition,
    ];
    return $this->derivatives;
  }

  /**
   * Returns list of 'Country Names' for select element.
   *
   * @return array
   *   Array of Country Names.
   */
  public static function getCountryNameOptions() {
    $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree("cit_countries_information");
    $country_names = [];
    foreach ($terms as $term) {
      $country_names[$term->name] = $term->name;
    }
    return $country_names;
  }

}
