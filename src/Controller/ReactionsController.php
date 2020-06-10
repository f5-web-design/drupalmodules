<?php

namespace Drupal\smart_content_paragraphs\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\smart_content_segments\Entity\SmartSegment;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Database\Connection;

/**
 * Class ReactionsController.
 *
 * @package Drupal\smart_content_paragraphs\Controller
 */
class ReactionsController extends ControllerBase {

  /**
   * The input parameters.
   *
   * @var string
   */
  private $inputParameters;

  /**
   * Database connection variable.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructor for ReactionsController objects.
   *
   * @param \Drupal\Core\Cache\RequestStack $requestStack
   *   Get the values from post request.
   * @param \Drupal\Core\Extension\Json $json
   *   The module handler to deal with json format.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity
   *   The Entity type manager service.
   * @param \Drupal\Core\Database\Connection $connection
   *   Database connectivity.
   */
  public function __construct(RequestStack $requestStack, Json $json, EntityTypeManagerInterface $entity, Connection $connection) {
    $request_parameters = $requestStack->getCurrentRequest()->getContent();
    $this->inputParameters = $json::decode($request_parameters);
    $this->nodeStorage = $entity->getStorage('node');
    $this->database = $connection;
  }

  /**
   * Create containers.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('serialization.json'),
      $container->get('entity_type.manager'),
      $container->get('database')
    );
  }

  /**
   * Get the reactions based on the conditions specified.
   */
  public function reaction($nid) {
    $response['data'] = [];
    // Looping the list of "Components" paragraph types.
    foreach ($this->getSmartComponents($nid) as $delta => $paragraph) {
      if (empty($response['data'][$delta])) {
        // Looping the list of "Variations - Smart Paragraph" paragraph types.
        foreach ($paragraph->get('field_variations')
          ->getValue() as $field_variation) {
          $variation_paragraph = Paragraph::load($field_variation['target_id']);

          if (($variation_paragraph->getType() == 'smart_content_paragraph')
          && (empty($response['data'][$delta]))) {
            $field_smart_content_conditions = $variation_paragraph->get('field_smart_content_conditions')
              ->getValue();
            if ($this->validateVariation($field_smart_content_conditions, $response, $field_variation, $delta)) {
              $response['data'][$delta] = $field_variation['target_id'];
            }
          }
        }
      }
    }

    return new JsonResponse($response);
  }

  /**
   * Function to check string starting with given substring.
   */
  public function startsWith($string, $startString) {
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
  }

  /**
   * Get the User's operating system.
   */
  public function getOs() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $os_platform = "Unknown OS Platform";
    $os_array = [
      '/windows nt 10/i' => 'windows',
      '/windows nt 6.3/i' => 'windows',
      '/windows nt 6.2/i' => 'windows',
      '/windows nt 6.1/i' => 'windows',
      '/windows nt 6.0/i' => 'windows',
      '/windows nt 5.2/i' => 'windows',
      '/windows nt 5.1/i' => 'windows',
      '/windows xp/i' => 'windows',
      '/windows nt 5.0/i' => 'windows',
      '/windows me/i' => 'windows',
      '/win98/i' => 'windows',
      '/win95/i' => 'windows',
      '/win16/i' => 'windows',
      '/macintosh|mac os x/i' => 'macosx',
      '/mac_powerpc/i' => 'macosx',
      '/linux/i' => 'linux',
      '/ubuntu/i' => 'Ubuntu',
      '/iphone/i' => 'ios',
      '/ipod/i' => 'ios',
      '/ipad/i' => 'ios',
      '/android/i' => 'android',
      '/blackberry/i' => 'BlackBerry',
      '/webos/i' => 'android',
    ];

    foreach ($os_array as $regex => $value) {
      if (preg_match($regex, $user_agent)) {
        $os_platform = $value;
      }
    }
    return $os_platform;
  }

  /**
   * Validate the conditions.
   */
  public function validateCondition($condition) {
    $inputParameters = $this->inputParameters;
    $value = $condition['conditions_type_settings']['value'];
    $negate = (bool) $condition['conditions_type_settings']['negate'];

    switch ($condition["type"]) {
      case 'type:textfield':
        $result = $this->evaluateTextfield($condition);
        break;

      case 'type:number':
        $result = $this->evaluateNumber($condition);
        break;

      case 'type:select':
        if ($condition['id'] == 'device:device_os') {
          $user_value = $this->getOs();
          $result = ($user_value == $value) & !$negate;
        }
        else {
          $result = $this->evaluateSelect($condition);
        }
        break;

      case 'type:node_reference':
        $result = in_array('node/' . $value, $inputParameters["pages"]) & !$negate;
        break;
    }
    return $result;
  }

  /**
   * Validate variations.
   */
  public function validateVariation($field_smart_content_conditions, $response, $field_variation, $delta) {
    // All conditions need to be satisfied in order to be displayed.
    $all_conditions_match = TRUE;
    foreach ($field_smart_content_conditions as $field_smart_content_condition) {
      $conditions =
        SmartSegment::load($field_smart_content_condition['target_id'])
          ->conditions_settings;

      foreach ($conditions as $variation_set_condition) {
        $all_conditions_match = $all_conditions_match
          && $this->validateCondition($variation_set_condition);
      }
    }
    return $all_conditions_match;
  }

  /**
   * Get smart content components.
   */
  public function getSmartComponents($nid) {
    $node = $this->nodeStorage->load($nid);
    $paragraph_field_items = $node->get('field_components')->getValue();

    $paragraph_ids = array_map(function ($b) {
      return $b['target_id'];
    }, $paragraph_field_items);

    $paragraph_entities = Paragraph::loadMultiple($paragraph_ids);

    // Returning only those from Smart type.
    return array_filter($paragraph_entities, function ($component) {
      return $component->getType() === 'smart';
    });
  }

  /**
   * Validating text field conditions.
   */
  public function evaluateTextfield($condition) {
    $user_value_key = $this->getKeynameFromPluginId($condition);
    $inputParameters = $this->inputParameters;
    // Value we're checking against.
    $value = strtolower($condition['conditions_type_settings']['value']);
    // Value in user's browser.
    $user_value = strtolower($inputParameters[$user_value_key]);
    // IF/ IF NOT for the condition.
    $negate = (bool) $condition['conditions_type_settings']['negate'];
    $op = $condition['conditions_type_settings']['op'];

    switch ($op) {
      case 'equals':
        $result = (strtolower($user_value) === strtolower($value));
        break;

      case 'starts_with':
        $result = $this->startsWith($user_value, $value);
        break;

      case 'empty':
        $result = empty($user_value);
        break;
    }

    return $result & !$negate;
  }

  /**
   * Validating number field conditions.
   */
  public function evaluateNumber($condition) {

    $user_value_key = $this->getKeynameFromPluginId($condition);
    $inputParameters = $this->inputParameters;
    // Value we're checking against.
    $settings = $condition['conditions_type_settings'];
    // Value we want to match.
    $value = $settings['value'];
    // Value in user's browser.
    $user_value = $inputParameters[$user_value_key];
    // IF/ IF NOT for the condition.
    $negate = (bool) $settings['negate'];

    $op = $settings['op'];

    switch ($op) {
      case 'eq':
        $result = ($user_value === $value);
        break;

      case 'gt':
        $result = ($user_value > $value);
        break;

      case 'lt':
        $result = ($user_value < $value);
        break;

      case 'gte':
        $result = ($user_value >= $value);
        break;

      case 'lte':
        $result = ($user_value <= $value);
        break;

    }

    return $result & !$negate;
  }

  /**
   * Get key name from plugin ID.
   */
  public function getKeynameFromPluginId($condition) {
    preg_match('/.*:(.*)/', $condition["id"], $matches);
    return $matches[1];
  }

  /**
   * Validating select field conditions.
   */
  public function evaluateSelect($condition) {
    $user_value_key = $this->getKeynameFromPluginId($condition);
    $user_value = $this->inputParameters[$user_value_key];
    $value = $condition['conditions_type_settings']['value'];
    // IF/IF NOT for the condition.
    $negate = (bool) $condition['conditions_type_settings']['negate'];

    if ($user_value_key == 'region') {
      return $this->validateRegion($value) & !$negate;
    }
    else {
      return (strtolower($user_value) == strtolower($value)) & !$negate;
    }
  }

  /**
   * Validate Regions.
   */
  public function validateRegion($region) {
    $query = $this->database->select('smart_content_paragraphs_regions', 'hww');
    $query->condition('hww.region', $region, '=');
    $query->fields('hww', [
      'boundsNElat',
      'boundsNElong',
      'boundsSWlat',
      'boundsSWlong',
    ]);
    $result = $query->execute()->fetchObject();

    $eastBound = $this->inputParameters['longitude'] < $result->boundsNElong;
    $westBound = $this->inputParameters['longitude'] > $result->boundsSWlong;

    if ($result->boundsNElong < $result->boundsSWlong) {
      $inLong = $eastBound || $westBound;
    }
    else {
      $inLong = $eastBound && $westBound;
    }

    $inLat = $this->inputParameters['latitude'] > $result->boundsSWlat
              && $this->inputParameters['latitude'] < $result->boundsNElat;
    return $inLat && $inLong;
  }

}
