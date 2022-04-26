<?php

namespace Drupal\pce_geobrowser\Plugin\smart_content\Condition\Type;

use Drupal\Core\Form\FormStateInterface;
use Drupal\smart_content\Condition\ConditionBase;
use Drupal\smart_content\Condition\Type\ConditionTypeBase;

/**
 * Provides a 'number' ConditionType.
 *
 * @SmartConditionType(
 *  id = "float_type",
 *  label = @Translation("Float Type"),
 * )
 */
class FloatType extends ConditionTypeBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $condition_definition = $this->conditionInstance->getPluginDefinition();
    $form = ConditionBase::attachNegateElement($form, $this->configuration);
    $form['op'] = [
      '#type' => 'select',
      '#options' => $this->getOperators(),
      '#default_value' => isset($this->configuration['op']) ? $this->configuration['op'] : $this->defaultFieldConfiguration()['op'],
    ];
    $form['value'] = [
      '#type' => 'number',
      '#required' => TRUE,
      '#step' => 0.00000001,
      '#attributes' => ['class' => ['float-type']],
      '#default_value' => isset($this->configuration['value']) ? $this->configuration['value'] : $this->defaultFieldConfiguration()['value'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultFieldConfiguration() {
    return [
      'op' => 'equals',
      'value' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getOperators() {
    return [
      'equals' => '=',
      'gt' => '>',
      'lt' => '<',
      'gte' => '>=',
      'lte' => '<=',
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
   * {@inheritdoc}
   */
  public function getHtmlSummary() {
    $configuration = $this->getConfiguration();
    $operator = $this->getOperators()[$configuration['op']];

    return [
      '#type' => 'markup',
      'op' => [
        '#markup' => "{$operator}",
        '#prefix' => '<span class="condition-type-op">',
        '#suffix' => '</span> ',
      ],
      'value' => [
        '#markup' => $configuration['value'],
        '#prefix' => '<span class="condition-type-value">"',
        '#suffix' => '"</span>',
      ],
    ];
  }

}
