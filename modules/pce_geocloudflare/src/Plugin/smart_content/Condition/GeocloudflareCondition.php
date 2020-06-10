<?php

namespace Drupal\pce_geocloudflare\Plugin\smart_content\Condition;

use Drupal\smart_content\Condition\ConditionTypeConfigurableBase;

/**
 * Provides a default Smart Condition.
 *
 * @SmartCondition(
 *   id = "geocloudflare",
 *   label = @Translation("Geolocation - Cloudflare"),
 *   group = "geocloudflare",
 *   weight = 0,
 *   deriver = "Drupal\pce_geocloudflare\Plugin\Derivative\GeocloudflareDerivative"
 * )
 */
class GeocloudflareCondition extends ConditionTypeConfigurableBase {

}
