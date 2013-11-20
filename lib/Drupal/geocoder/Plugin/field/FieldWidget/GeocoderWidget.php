<?php

/**
 * @file
 * Contains \Drupal\geocoder\Plugin\Field\FieldWidget\GeocoderWidget.
 */

namespace Drupal\geocoder\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Widget implementation of the 'geocoder_default' widget.
 *
 * @FieldWidget(
 *   id = "geocoder_widget",
 *   label = @Translation("Geocoder"),
 *   field_types = {
 *     "text"
 *   },
 *   settings = {
 *     "destination_field" = "",
 *     "placeholder" = "",
 *     "geocoder_engine" = ""
 *   }
 * )
 */
class GeocoderWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, array &$form_state) {
    $element = array();

    $entityFieldDefinitions = \Drupal::entityManager()->getFieldDefinitions($this->fieldDefinition->entity_type, $this->fieldDefinition->bundle);

    $options = array();
    foreach ($entityFieldDefinitions as $id => $definition) {
      if ($definition['type'] == 'field_item:geofield') {
        $options[$id] = $definition['label'];
      }
    }

    $element['destination_field'] = array(
      '#type' => 'select',
      '#title' => $this->t('Destination Geo Field'),
      '#default_value' => $this->getSetting('destination_field'),
      '#required' => TRUE,
      '#options' => $options,
    );

    $element['placeholder'] = array(
      '#type' => 'textfield',
      '#title' => t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    );

    /*$element['size'] = array(
      '#type' => 'number',
      '#title' => t('Size of textfield'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 1,
    );
    */
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $summary[] = $this->t('Destination Geofield: !destination', array('!destination' => $this->getSetting('destination_field')));
    $placeholder = $this->getSetting('placeholder');
    if (!empty($placeholder)) {
      $summary[] = t('Placeholder: @placeholder', array('@placeholder' => $placeholder));
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, array &$form_state) {
    $main_widget = $element + array(
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
      '#placeholder' => $this->getSetting('placeholder'),
      '#attributes' => array('class' => array('geocoder-source')),
    );

    $element['value'] = $main_widget;

    $source_field_id = 'edit-' . str_replace('_', '-', $element['#field_name']) . '-' . $delta . '-value';
    $destination_field_id = 'edit-' . str_replace('_', '-', $this->getSetting('destination_field')) . '-wrapper';

    $element['#attached'] = array(
      'library' => array(
        array('system', 'jquery.ui.autocomplete'),
      ),
      'js' => array(
        'sites/all/libraries/geocoder-js/dist/geocoder.js',
        drupal_get_path('module', 'geocoder') . '/js/geocoderWidget.js',
        array(
          'data' => array(
            'geocoder' => array(
              'engine' => 'google',
              'fields' => array(
                array(
                  'sourceField' => $source_field_id,
                  'destinationField' => $destination_field_id,
                  'sourceType' => 'geofield',
                )
              ),
            ),
          ),
          'type' => 'setting',
        ),
      ),
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, array &$form_state) {
    if ($violation->arrayPropertyPath == array('format') && isset($element['format']['#access']) && !$element['format']['#access']) {
      // Ignore validation errors for formats if formats may not be changed,
      // i.e. when existing formats become invalid. See filter_process_format().
      return FALSE;
    }
    return $element;
  }
}
