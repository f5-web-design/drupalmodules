<?php

namespace Drupal\pce_geolocation\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure site information settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'smart_content_paragraphs.pce_geolocation.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'smart_content_paragraphs_pce_geolocation_settings';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('smart_content_paragraphs.pce_geolocation.settings');
    $form['smart_content_paragraphs'] = [
      '#type' => 'fieldset',
      '#title' => $this
        ->t('Country Code Detection'),
    ];
    $form['smart_content_paragraphs']['country_http_header'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Country detection HTTP header'),
      '#description'   => $this->t('This is the HTTP header containing the country detection details. e.g. "HTTP_CF_IPCOUNTRY" or "HTTP_X_AKAMAI_EDGESCAPE"'),
      '#required'      => TRUE,
      '#default_value' => $config->get('country_http_header') ?? '',
    ];
    $form['smart_content_paragraphs']['country_http_header_property'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Country detection HTTP header property'),
      '#description'   => $this->t('This is the property inside the HTTP header defined above, that contains the country code. e.g. "country_code" when using the  "HTTP_X_AKAMAI_EDGESCAPE" header. Leave it empty if the country code is directly set in the HTTP header.'),
      '#default_value' => $config->get('country_http_header_property') ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->config('smart_content_paragraphs.pce_geolocation.settings')
        ->set('country_http_header', $form_state->getValue('country_http_header'))
        ->set('country_http_header_property', $form_state->getValue('country_http_header_property'))
        ->save();
  }

}
