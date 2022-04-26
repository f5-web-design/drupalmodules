<?php

namespace Drupal\pce_geobrowser\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locales;

/**
 * Deriver for GeobrowserCondition.
 *
 * Provides a deriver for
 * Drupal\pce_geobrowser\Plugin\smart_content\Condition\GeobrowserCondition.
 * Definitions are based on properties available in JS from user's browser.
 */
class GeobrowserDerivative extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [
      'region' => [
        'label' => 'Region',
        'type' => 'select',
        'options_callback' => [get_class($this), 'getRegionOptions'],
      ] + $base_plugin_definition,
      'latitude' => [
        'label' => 'Latitude',
        'type' => 'float_type',
      ] + $base_plugin_definition,
      'longitude' => [
        'label' => 'Longitude',
        'type' => 'float_type',
      ] + $base_plugin_definition,
    ];
    return $this->derivatives;
  }

  /**
   * Returns list of 'Regions' for select element.
   *
   * @return array
   *   Array of Regions.
   */
  public static function getRegionOptions() {
    $file = fopen(drupal_get_path('module', 'smart_content_paragraphs') . '/data/region_codes.csv', "r");
    $region_codes = [];
    while (!feof($file)) {
      $regions = fgetcsv($file);
      if (!empty($regions[2])) {
        $country_name = self::getCountryNameFromCode($regions[0]);
        $region_codes[$country_name][$regions[2]] = $regions[2];
      }
    }
    ksort($region_codes);
    foreach ($region_codes as $key => $value) {
      ksort($value);
      $region_codes[$key] = $value;
    }
    return $region_codes;
  }

  /**
   * Getting country name from Country code.
   *
   * @return array
   *   Array of Country Name.
   */
  public static function getCountryNameFromCode($country_code) {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $isValidLocale = Locales::exists($language);
    $locale = $isValidLocale ? $language : 'en';
    \Locale::setDefault($locale);
    $countries = Countries::getNames();
    foreach ($countries as $key => $value) {
      if ($key == $country_code) {
        return $value;
      }
    }
  }

}
