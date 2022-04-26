<?php

namespace Drupal\smart_content_paragraphs\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\field\FieldConfigInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\smart_content\Entity\SegmentSetConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
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
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * Database service object.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Constructor for ReactionsController objects.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity
   *   The Entity type manager service.
   * @param \Drupal\Core\Database\Connection $connection
   *   Database connectivity.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager service.
   */
  public function __construct(Request $request, EntityTypeManagerInterface $entity_type_manager, Connection $database, EntityFieldManagerInterface $entity_field_manager) {
    $this->inputParameters = Json::decode($request->getContent());
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->database = $database;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * Create containers.
   */
  public static function create(ContainerInterface $container) {
    return new static(
          $container->get('request_stack')->getCurrentRequest(),
          $container->get('entity_type.manager'),
          $container->get('database'),
          $container->get('entity_field.manager')
      );
  }

  /**
   * Get the reactions based on the conditions specified.
   */
  public function reaction($nid) {
    $response['data'] = [];
    // Looping the list of "Components" paragraph types.
    foreach ($this->getSmartComponents($nid) as $smart_components) {
      foreach ($smart_components as $delta => $paragraph) {
        if (empty($response['data'][$delta])) {
          // Looping the list of "Variations - Smart Paragraph" paragraph types.
          foreach ($paragraph->get('field_variations')
            ->getValue() as $field_variation) {
            $variation_paragraph = Paragraph::load($field_variation['target_id']);
            if (($variation_paragraph->getType() == 'smart_content_paragraph')) {
              $field_smart_content_conditions = $variation_paragraph->get('field_smart_content_conditions')
                ->getValue();
              if ($this->validateVariation($field_smart_content_conditions, $response, $field_variation, $delta)) {
                $response['data'][$delta][] = $field_variation['target_id'];
              }
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
    $value = $condition['condition_type_settings']['value'];
    $negate = (bool) $condition['condition_type_settings']['negate'];

    switch ($condition["type"]) {
      case 'type:textfield':
        $result = $this->evaluateTextfield($condition);
        break;

      case 'type:number':
        $result = $this->evaluateNumber($condition);
        break;

      case 'type:float_type':
        $result = $this->evaluateNumber($condition);
        break;

      case 'type:select':
        if ($condition['id'] == 'device:device_os') {
          $user_value = $this->getOs();
          $result = (($user_value == $value) == !$negate);
        }
        else {
          $result = $this->evaluateSelect($condition);
        }
        break;

      case 'type:node_reference':
        $result = (in_array('node/' . $value, $inputParameters["pages"]) == !$negate);
        break;
    }

    return $result;
  }

  /**
   * Validate variations.
   */
  public function validateVariation($field_smart_content_conditions, $response, $field_variation, $delta) {
    // All conditions need to be satisfied in order to be displayed.
    $group = [];

    foreach ($field_smart_content_conditions as $field_smart_content_condition) {
      $segment_set = SegmentSetConfig::load($field_smart_content_condition['target_id'])->getSegmentSet()->getSegments();
      foreach ($segment_set as $segment_id => $segment) {
        $conditions = $segment->get('conditions');
        foreach ($conditions as $condition_id => $condition) {
          $group[] = $conditions[$condition_id]['conditions'];
          end($group);
          $group[key($group)]['op'] = $conditions[$condition_id]['op'];
        }
      }
      $segmentResult = [];
      foreach ($group as $variation_set_condition) {
        $validateResult = [];
        $all_conditions_match = TRUE;
        foreach ($variation_set_condition as $key => $condition) {
          if ($key !== 'op') {
            $validateResult[] = $this->validateCondition($condition);
            $all_conditions_match = $all_conditions_match && $this->validateCondition($condition);
          }
          else {
            if ($variation_set_condition[$key] == 'OR' && count(array_unique($validateResult)) == 1 && $all_conditions_match == FALSE) {
              $all_conditions_match = FALSE;
            }
            elseif ($variation_set_condition[$key] == 'OR' && count(array_unique($validateResult)) > 1) {
              $all_conditions_match = TRUE;
            }
          }
        }
        $segmentResult[] = $all_conditions_match;
        if (count(array_unique($segmentResult)) == 1 && $all_conditions_match == FALSE) {
          $all_conditions_match = FALSE;
        }
        elseif (count(array_unique($segmentResult)) > 1) {
          $all_conditions_match = TRUE;
        }
      }
    }
    return $all_conditions_match;
  }

  /**
   * Get smart content components.
   */
  public function getSmartComponents($nid) {
    $node = $this->nodeStorage->load($nid);
    $field_definitions = $this->entityFieldManager->getFieldDefinitions('node', $node->getType());
    $paragraphs = [];

    foreach ($field_definitions as $fieldName => $fieldConfig) {
      if ($fieldConfig->getType() == 'entity_reference_revisions') {
        $paragraph_ids = array_map(
              function ($b) {
                  return $b['target_id'];
              }, $node->get($fieldName)->getValue()
          );

        $paragraphs[] = array_filter(
              Paragraph::loadMultiple($paragraph_ids), function ($component) {
                  return $component->getType() === 'smart';
              }
          );
      }
    }
    return $paragraphs;
  }

  /**
   * Helper function to Get all fields of content type.
   */
  public function getContentTypeFields($contentType) {

    $fields = [];
    if (!empty($contentType)) {
      $fields = array_filter(
            \Drupal::service('entity_field.manager')
              ->getFieldDefinitions('node', $contentType), static function ($field_definition) {
                return $field_definition instanceof FieldConfigInterface;
              }
        );
    }

    return $fields;
  }

  /**
   * Validating text field conditions.
   */
  public function evaluateTextfield($condition) {
    $user_value_key = $this->getKeynameFromPluginId($condition);
    $inputParameters = $this->inputParameters;
    // Value we're checking against.
    $value = strtolower($condition['condition_type_settings']['value']);
    // Value in user's browser.
    $user_value = strtolower($inputParameters[$user_value_key]);
    // IF/ IF NOT for the condition.
    $negate = (bool) $condition['condition_type_settings']['negate'];
    $op = $condition['condition_type_settings']['op'];

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

    return ($result == !$negate);
  }

  /**
   * Validating number field conditions.
   */
  public function evaluateNumber($condition) {

    $user_value_key = $this->getKeynameFromPluginId($condition);
    $inputParameters = $this->inputParameters;
    // Value we're checking against.
    $settings = $condition['condition_type_settings'];
    // Value we want to match.
    $value = $settings['value'];
    // Value in user's browser.
    $user_value = $inputParameters[$user_value_key];
    // IF/ IF NOT for the condition.
    $negate = (bool) $settings['negate'];
    $op = $settings['op'];

    switch ($op) {
      case 'equals':
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
    return ($result == !$negate);
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
    $value = $condition['condition_type_settings']['value'];
    // IF/IF NOT for the condition.
    $negate = (bool) $condition['condition_type_settings']['negate'];
    if ($user_value_key == 'region') {
      return ($this->validateRegion($value) == !$negate);
    }
    else {
      return ((strtolower($user_value) == strtolower($value)) == !$negate);
    }
  }

  /**
   * Validate Regions.
   */
  public function validateRegion($region) {
    $query = $this->database->select('smart_content_paragraphs_regions', 'hww');
    $query->condition('hww.region', $region, '=');
    $query->fields(
          'hww', [
            'boundsNElat',
            'boundsNElong',
            'boundsSWlat',
            'boundsSWlong',
          ]
      );
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
