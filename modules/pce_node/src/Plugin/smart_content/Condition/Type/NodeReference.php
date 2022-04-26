<?php

namespace Drupal\pce_node\Plugin\smart_content\Condition\Type;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\smart_content\Condition\ConditionBase;
use Drupal\smart_content\Condition\Type\ConditionTypeBase;

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

  /**
   * Get the options for this select element.
   *
   * @return array
   *   The array of options.
   */
  public function getFormOptions() {
    $condition_definition = $this->conditionInstance->getPluginDefinition();
    $options = [];
    // If 'options' are defined in definition, populate options.
    if (isset($condition_definition['options'])) {
      $options = $condition_definition['options'];
    }
    // If 'options_callback' is defined in definition, validate and populate
    // options.
    elseif (isset($condition_definition['options_callback'])) {
      // Confirm 'options_callback' is callable function/method.
      if (is_callable($condition_definition['options_callback'], FALSE, $callable_name)) {
        $options = call_user_func($condition_definition['options_callback'], $this->conditionInstance);
      }
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getHtmlSummary() {
    $configuration = $this->getConfiguration();
    $options = $this->getFormOptions();
    $value = $configuration['value'];
    if (isset($options[$configuration['value']])) {
      $value = $options[$configuration['value']];
    }
    return [
      '#type' => 'markup',
      'op' => [
        '#markup' => $this->t('equals'),
        '#prefix' => '<span class="condition-type-op">',
        '#suffix' => '</span> ',
      ],
      'value' => [
        '#markup' => "$value",
        '#prefix' => '<span class="condition-type-value">"',
        '#suffix' => '"</span>',
      ],
    ];
  }

}
