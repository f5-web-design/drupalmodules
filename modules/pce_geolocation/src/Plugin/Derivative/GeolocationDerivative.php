<?php

namespace Drupal\pce_geolocation\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locales;

/**
 * Deriver for GeolocationCondition.
 *
 * Provides a deriver for
 * Drupal\pce_geolocation\Plugin\smart_content\Condition\GeolocationCondition.
 * Definitions are based on user's browser's header value.
 */
class GeolocationDerivative extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [
      'countryName' => [
        'label' => 'Country Name',
        'type' => 'select',
        'options_callback' => [get_class($this), 'getOptions'],
      ] + $base_plugin_definition,
    ];
    return $this->derivatives;
  }

  /**
   * Returns list of 'Country Names' for select element.
   *
   * @return array
   *   Array of Country Names.
   */
  public static function getOptions() {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $isValidLocale = Locales::exists($language);
    $locale = $isValidLocale ? $language : 'en';
    \Locale::setDefault($locale);
    $countries = Countries::getNames();
    return $countries;
  }

}
