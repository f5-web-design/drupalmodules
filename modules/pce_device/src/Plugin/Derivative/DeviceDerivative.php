<?php

namespace Drupal\pce_device\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Deriver for DeviceCondition.
 *
 * Provides a deriver for
 * Drupal\pce_device\Plugin\smart_content\Condition\DeviceCondition.
 * Definitions are based on properties available in JS from user's browser.
 */
class DeviceDerivative extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [
      'device_type' => [
        'label' => 'Device Type',
        'type' => 'select',
        'options_callback' => [get_class($this), 'getDeviceOptions'],
      ] + $base_plugin_definition,
      'device_os' => [
        'label' => 'Operating System',
        'type' => 'select',
        'options_callback' => [get_class($this), 'getOsOptions'],
      ] + $base_plugin_definition,
      'device_width' => [
        'label' => 'Device Width',
        'type' => 'number',
        'format_options' => [
          'suffix' => 'px',
        ],
      ] + $base_plugin_definition,
      'device_height' => [
        'label' => 'Device Height',
        'type' => 'number',
        'format_options' => [
          'suffix' => 'px',
        ],
      ] + $base_plugin_definition,
    ];
    return $this->derivatives;
  }

  /**
   * Returns list of 'Device options' for select element.
   *
   * @return array
   *   Array of Device options.
   */
  public static function getDeviceOptions() {
    return [
      'mobile' => t('Mobile'),
      'tablet' => t('Tablet'),
      'desktop' => t('Desktop'),
    ];
  }

  /**
   * Returns list of 'Operating Systems' for select element.
   *
   * @return array
   *   Array of Operation Systems.
   */
  public static function getOsOptions() {
    return [
      'android' => t('Android'),
      'chromeos' => t('ChromeOS'),
      'ios' => t('iOS'),
      'linux' => t('Linux'),
      'macosx' => t('Mac OS X'),
      'nintendo' => t('Nintendo'),
      'playstation' => t('PlayStation'),
      'windows' => t('Windows'),
    ];
  }

}
