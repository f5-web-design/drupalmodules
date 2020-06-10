<?php

namespace Drupal\pce_device\Plugin\smart_content\Condition;

use Drupal\smart_content\Condition\ConditionTypeConfigurableBase;

/**
 * Provides a default Smart Condition.
 *
 * @SmartCondition(
 *   id = "device",
 *   label = @Translation("Device"),
 *   group = "device",
 *   weight = 0,
 *   deriver = "Drupal\pce_device\Plugin\Derivative\DeviceDerivative"
 * )
 */
class DeviceCondition extends ConditionTypeConfigurableBase {

}
