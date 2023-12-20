<?php

namespace Drupal\bootstrap_horizontal_tabs\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'bootstrap_horizontal_tabs' field type.
 *
 * @FieldType(
 *   id = "bootstrap_horizontal_tabs",
 *   label = @Translation("Horizontal Tabs"),
 *   description = @Translation("Horizontal Tabs"),
 *   category = @Translation("Text"),
 *   default_widget = "bootstrap_horizontal_tabs",
 *   default_formatter = "bootstrap_horizontal_tabs"
 * )
 */
class BootstrapHorizontalTabs extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['header'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Tab header'))
      ->setRequired(FALSE);
    $properties['body_value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Body value'))
      ->setRequired(FALSE);
    $properties['body_format'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Body format'))
      ->setRequired(FALSE);
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'header' => [
          'type' => 'varchar',
          'length' => 512,
          'binary' => FALSE,
        ],
        'body_value' => [
          'type' => 'text',
          'size' => 'normal',
          'binary' => FALSE,
        ],
        'body_format' => [
          'type' => 'varchar',
          'length' => 512,
          'binary' => FALSE,
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values['header'] = "Tab Header";
    $values['body_value'] = 'Lorem ipsum dolor sit amet.';
    $values['body_format'] = 'basic_html';
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $header = $this->get('header')->getValue();
    $body = $this->get('body_value')->getValue();
    return empty($body) && empty($header);
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();
    $manager = $this->getTypedDataManager()->getValidationConstraintManager();

    $constraints[] = $manager->create('ComplexData', [
      'header' => [
        'NotBlank' => [
          'message' => $this->t('This value should not be blank when %body_value_label has a value.', [
            '%body_value_label' => 'Tab Body',
          ]),
        ],
      ],
    ]);

    return $constraints;
  }

}
