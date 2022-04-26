<?php

namespace Drupal\pce_node\Plugin\smart_content\ConditionType;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\smart_content\Condition\ConditionBase;
use Drupal\smart_content\ConditionType\ConditionTypeBase;

/**
 * Provides a 'number' ConditionType.
 *
 * @SmartConditionType(
 *  id = "node_reference",
 *  label = @Translation("Node Reference"),
 * )
 */
class NodeReference extends ConditionTypeBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    if ($nid = !empty($this->configuration['value']) ? $this->configuration['value'] : NULL) {
      $node = Node::load($nid);
    }

    $condition_definition = $this->conditionInstance->getPluginDefinition();
    $form = ConditionBase::attachNegateElement($form, $this->configuration);

    $form['label'] = [
      '#type' => 'container',
      // @todo get condition group name from group
      '#markup' => $condition_definition['label'] . '(' . $condition_definition['group'] . ')',
      '#attributes' => ['class' => ['condition-label']],
    ];
    $form['value'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#default_value' => !empty($node) ? $node : NULL,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultFieldConfiguration() {
    return [
      'value' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries() {
    return ['smart_content/condition_type.standard'];
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachedSettings() {
    return $this->getConfiguration() + $this->defaultFieldConfiguration();
  }

}
