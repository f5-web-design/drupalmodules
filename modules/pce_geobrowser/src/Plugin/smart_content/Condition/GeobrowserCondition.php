<?php

namespace Drupal\pce_geobrowser\Plugin\smart_content\Condition;

use Drupal\smart_content\Condition\ConditionTypeConfigurableBase;

/**
 * Provides a default Smart Condition.
 *
 * @SmartCondition(
 *   id = "geobrowser",
 *   label = @Translation("Geolocation - Browser"),
 *   group = "geobrowser",
 *   weight = 0,
 *   deriver = "Drupal\pce_geobrowser\Plugin\Derivative\GeobrowserDerivative"
 * )
 */
class GeobrowserCondition extends ConditionTypeConfigurableBase {

}
