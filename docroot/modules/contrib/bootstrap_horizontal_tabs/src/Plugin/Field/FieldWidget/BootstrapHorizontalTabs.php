<?php

namespace Drupal\bootstrap_horizontal_tabs\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Plugin implementation of the 'bootstrap_horizontal_tabs' widget.
 *
 * @FieldWidget(
 *   id = "bootstrap_horizontal_tabs",
 *   label = @Translation("Horizontal Tabs"),
 *   field_types = {
 *     "bootstrap_horizontal_tabs"
 *   }
 * )
 */
class BootstrapHorizontalTabs extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['header'] = [
      '#title' => 'Tab Label',
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->header) ? $items[$delta]->header : NULL,
      '#size' => '60',
      '#placeholder' => '',
      '#maxlength' => 255,
    ];
    $element['body'] = [
      '#title' => 'Tab Body',
      '#type' => 'text_format',
      '#default_value' => isset($items[$delta]->body_value) ? $items[$delta]->body_value : NULL,
      '#format' => $items[$delta]->body_format,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $headers = [];
    // This loop is through (potential) field instances.
    foreach ($values as &$value) {
      $headers[] = trim($value['header']);
      // Split the "text_format" form element data into our field's schema.
      $value['body_value'] = trim($value['body']['value']);
      $value['body_format'] = $value['body']['format'];
    }
    // Validate header values are unique.
    $headers = array_filter($headers);
    $unique = array_unique($headers);
    $duplicates = array_diff_assoc($headers, $unique);
    $duplicate_keys = array_keys(array_intersect($headers, $duplicates));
    $field_name = $this->fieldDefinition->getName();
    foreach ($duplicate_keys as $key) {
      $form_state->setError($form[$field_name]['widget'][$key]['header'], t('Tab header has to be unique'));
    }

    return $values;
  }

  /**
   * {@inheritdoc}
   *
   * @see Drupal\text\Plugin\Field\FieldWidget\TextareaWithSummaryWidget.php
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    $element = parent::errorElement($element, $violation, $form, $form_state);
    return ($element === FALSE) ? FALSE : $element[$violation->arrayPropertyPath[0]];
  }

}
