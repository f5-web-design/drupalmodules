<?php

namespace Drupal\pce_geocloudflare\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locales;

/**
 * Deriver for GeocloudflareCondition.
 *
 * Provides a deriver for
 * Drupal\pce_geocloudflare\Plugin\smart_content\Condition\GeocloudflareCondition.
 * Definitions are based on user's browser's cloudflare header value.
 */
class GeocloudflareDerivative extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [
      'cloudfarecountry' => [
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
