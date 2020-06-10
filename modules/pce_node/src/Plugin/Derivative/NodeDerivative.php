<?php

namespace Drupal\pce_node\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Deriver for CookieCondition.
 *
 * Provides a deriver for
 * Drupal\pce_node\Plugin\smart_content\Condition\CookieCondition.
 * Definitions are based on properties available in JS from user's browser.
 */
class NodeDerivative extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [
      'has_visited_page' => [
        'label' => 'Has visited page',
        'type' => 'node_reference',
      ] + $base_plugin_definition,
    ];
    return $this->derivatives;
  }

}
