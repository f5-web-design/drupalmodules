<?php

namespace Drupal\pce_geolocation\Plugin\smart_content\Condition;

use Drupal\smart_content\Condition\ConditionTypeConfigurableBase;

/**
 * Provides a default Smart Condition.
 *
 * @SmartCondition(
 *   id = "geolocation",
 *   label = @Translation("Geolocation"),
 *   group = "geolocation",
 *   weight = 0,
 *   deriver = "Drupal\pce_geolocation\Plugin\Derivative\GeolocationDerivative"
 * )
 */
class GeolocationCondition extends ConditionTypeConfigurableBase {

}
