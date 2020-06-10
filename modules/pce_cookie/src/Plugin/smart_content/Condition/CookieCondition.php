<?php

namespace Drupal\pce_cookie\Plugin\smart_content\Condition;

use Drupal\smart_content\Condition\ConditionTypeConfigurableBase;

/**
 * Provides a default Smart Condition.
 *
 * @SmartCondition(
 *   id = "cookie",
 *   label = @Translation("Cookie"),
 *   group = "cookie",
 *   weight = 0,
 *   deriver = "Drupal\pce_cookie\Plugin\Derivative\CookieDerivative"
 * )
 */
class CookieCondition extends ConditionTypeConfigurableBase {

}
