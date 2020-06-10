<?php

namespace Drupal\pce_node\Plugin\smart_content\Condition;

use Drupal\smart_content\Condition\ConditionTypeConfigurableBase;

/**
 * Provides a default Smart Condition.
 *
 * @SmartCondition(
 *   id = "node",
 *   label = @Translation("node"),
 *   group = "node",
 *   weight = 0,
 *   deriver = "Drupal\pce_node\Plugin\Derivative\NodeDerivative"
 * )
 */
class NodeCondition extends ConditionTypeConfigurableBase {

}
