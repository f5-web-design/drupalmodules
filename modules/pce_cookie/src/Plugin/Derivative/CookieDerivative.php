<?php

namespace Drupal\pce_cookie\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Deriver for CookieCondition.
 *
 * Provides a deriver for
 * Drupal\pce_cookie\Plugin\smart_content\Condition\CookieCondition.
 * Definitions are based on properties available in JS from user's browser.
 */
class CookieDerivative extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [
      'no_of_pages_visited' => [
        'label' => 'Number of pages the user has seen',
        'type' => 'number',
      ] + $base_plugin_definition,
      'longest_session' => [
        'label' => 'Session Time',
        'type' => 'number',
        'format_options' => [
          'suffix' => '  minute(s)',
        ],
      ] + $base_plugin_definition,
    ];
    return $this->derivatives;
  }

}
