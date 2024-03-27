<?php

namespace Drupal\views_taxonomy_radios_filter\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Plugin\views\filter\TaxonomyIndexTid as TaxonomyIndexTidFilter;

/**
 * Display a radios/checkboxes filter for Taxonomy term id.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("taxonomy_index_tid_radios")
 */
class TaxonomyIndexTid extends TaxonomyIndexTidFilter {

  /**
   * {@inheritDoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $views_ui_key = \Drupal::config('views.settings')->get('ui.exposed_filter_any_label') ?? 'new_any';
    $views_ui_labels = ['old_any' => '<Any>', 'new_any' => '- Any -'];
    $options['all_label'] = ['default' => $views_ui_labels[$views_ui_key] ?? t('All')];

    return $options;
  }

  /**
   * {@inheritDoc}
   */
  public function buildExtraOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildExtraOptionsForm($form, $form_state);

    $form['type']['#options']['radios'] = $this->t('Radios/Checkboxes');

    $form['all_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t("'All' value's label"),
      '#default_value' => $this->options['all_label'],
      '#size' => 25,
      '#states' => [
        'visible' => [
          ':input[name="options[type]"]' => ['value' => 'radios'],
        ],
        'required' => [
          ':input[name="options[type]"]' => ['value' => 'radios'],
        ],
      ],
    ];
  }

  /**
   * {@inheritDoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    parent::valueForm($form, $form_state);
    
    if (!$form_state->get('exposed')) {
      return;
    }
    
    $multiple = $this->options['expose']['multiple'] ?? FALSE;
    $identifier = $this->options['expose']['identifier'] ?? FALSE;

    if ($this->options['type'] == 'radios') {
      $form['value']['#type'] = $multiple ? 'checkboxes' : 'radios';
      unset($form['value']['#multiple'], $form['value']['#size']);

      // Custom "All" label for better UI.
      $form['value']['#all_label'] = $this->options['all_label'];

      // Reapply custom form element to form.
      $form[$identifier] = $form['value'];
    }
  }

  /**
   * {@inheritDoc}
   */
  protected function exposedTranslate(&$form, $type) {
    $original_filter_type = $form['#type'];

    parent::exposedTranslate($form, $type);
    
    // Views base plugin transformed radios and checkboxes to select element.
    // Let's transform them back to what they're supposed to be.
    // Limit to $type == 'value' to apply on fields only (e.g. exclude 'operator').
    // @see https://www.drupal.org/project/views_taxonomy_radios_filter/issues/3306011
    if ($type == 'value' && in_array($original_filter_type, ['radios', 'checkboxes'])) {      
      $multiple = $this->options['expose']['multiple'] ?? FALSE;
      $form['#type'] = $multiple ? 'checkboxes' : 'radios';
    }
  }
  
}
