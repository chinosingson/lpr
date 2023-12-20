<?php

namespace Drupal\bootstrap_horizontal_tabs\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html as HtmlUtility;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Template\Attribute;

/**
 * Plugin implementation of the 'bootstrap_horizontal_tabs' formatter.
 *
 * @FieldFormatter(
 *   id = "bootstrap_horizontal_tabs",
 *   label = @Translation("Horizontal Tabs"),
 *   field_types = {
 *     "bootstrap_horizontal_tabs"
 *   }
 * )
 */
class BootstrapHorizontalTabs extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'tab_display' => 'tabs',
      'tab_orientation' => 'horizontal',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['tab_display'] = [
      '#type' => 'select',
      '#title' => $this->t('Header display'),
      '#options' => [
        'tabs' => $this->t('Tabs'),
        'pills' => $this->t('Pill buttons'),
      ],
      '#default_value' => $this->getSetting('tab_display'),
    ];
    $elements['tab_orientation'] = [
      '#type' => 'select',
      '#title' => $this->t('Header orientation'),
      '#options' => [
        'horizontal' => $this->t('Horizontal'),
        'vertical' => $this->t('Vertical'),
      ],
      '#default_value' => $this->getSetting('tab_orientation'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $settings = $this->getSettings();

    if (!empty($settings['tab_display'])) {
      $summary[] = t('Header display: @display', ['@display' => $settings['tab_display']]);
    }
    if (!empty($settings['tab_orientation'])) {
      $summary[] = t('Header orientation: @display', ['@display' => $settings['tab_orientation']]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    if (empty($items->getValue())) {
      return [];
    }
    $elements = [
      '#theme' => 'field__bootstrap_horizontal_tabs',
    ];

    // In cases where there is only one "tab," content should be rendered as a
    // single field with no Bootstrap attributes.
    $has_multiple = TRUE;
    if (count($items) === 1) {
      $has_multiple = FALSE;
    }

    $tabs_wrapper_attributes = [];
    $content_wrapper_attributes = [];
    if ($has_multiple) {
      // Convert settings to values for use.
      $settings = $this->getSettings();
      $tab_display = $settings['tab_display'] ?? 'tabs';
      $tab_orientation = $settings['tab_orientation'] === 'vertical' ? 'flex-column' : '';
      // Generate a unique id for the tabs instance.
      $instance_id = HtmlUtility::getUniqueId('bootstrap-horizontal-tabs');
      // Create new tabs wrapper attributes object for use in the template.
      $tabs_wrapper_attributes = [
        'class' => [
          'nav',
          'nav-' . $tab_display,
          $tab_orientation,
        ],
        'id' => $instance_id,
        'role' => 'tablist',
      ];
      // Create new content wrapper attributes object for use in the template.
      $content_wrapper_attributes = [
        'class' => [
          'tab-content',
        ],
        'id' => $instance_id . '-content',
      ];
    }
    $elements['#tabs_wrapper_attributes'] = new Attribute($tabs_wrapper_attributes);
    $elements['#content_wrapper_attributes'] = new Attribute($content_wrapper_attributes);

    // Process each field item.
    foreach ($items as $delta => $item) {

      // Add field value content to item delta.
      $elements[$delta] = [
        'header' => [
          '#markup' => $item->header,
        ],
        'body' => [
          '#type' => 'processed_text',
          '#text' => $item->body_value,
          '#format' => $item->body_format,
          '#langcode' => $item->getLangcode(),
        ],
      ];

      $tabs_item_attributes = [];
      $header_attributes = [];
      $body_attributes = [];
      if ($has_multiple) {
        // Generate a unique id for every tab header.
        // Transliterate the header (#3293143) following Drupal core's
        // approach in migrate/process/MachineName.php
        $transformed = \Drupal::transliteration()->transliterate($item->header, LanguageInterface::LANGCODE_DEFAULT);
        $item_header_text = preg_replace('/[^a-z0-9_]+/', '_', strtolower($transformed));
        $unique_item_id = HtmlUtility::getUniqueId($item_header_text);
        // Create new body attributes object for use in the template.
        $body_attributes = [
          'aria-labelledby' => $unique_item_id . '-tab',
          'class' => [
            'tab-pane',
            'fade',
          ],
          'id' => $unique_item_id,
          'role' => 'tabpanel',
        ];
        // Create new header attributes object for use in the template.
        $header_attributes = [
          'aria-selected' => 'false',
          'class' => [
            'nav-link',
          ],
          'data-toggle' => 'tab',
          'href' => '#' . $unique_item_id,
          'id' => $unique_item_id . '-tab',
          'role' => 'tab',
        ];
        // Create new tabs item attributes object for use in the template.
        $tabs_item_attributes = [
          'class' => [
            'nav-item',
          ],
          'role' => 'tab',
        ];
      }
      $elements[$delta]['#body_attributes'] = new Attribute($body_attributes);
      $elements[$delta]['#header_attributes'] = new Attribute($header_attributes);
      $elements[$delta]['#tabs_item_attributes'] = new Attribute($tabs_item_attributes);

      // Add active/selected states to first of multiple items.
      if ($delta === 0 && $has_multiple) {
        $elements[$delta]['#tabs_item_attributes']->addClass('active');
        $elements[$delta]['#header_attributes']->addClass('active');
        $elements[$delta]['#header_attributes']->setAttribute('aria-selected', 'true');
        $elements[$delta]['#body_attributes']->addClass('active');
        $elements[$delta]['#body_attributes']->addClass('show');
        $elements[$delta]['#body_attributes']->addClass('in');
      }
    }

    return $elements;
  }

}
