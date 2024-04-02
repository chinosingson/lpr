<?php

namespace Drupal\bootstrap_horizontal_tabs\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure settings for the utnews module.
 */
class BaseConfigurationForm extends ConfigFormBase {

  /**
   * The form options.
   *
   * @var array
   */
  public static $versions = [
    '3' => 'Bootstrap 3',
    '4' => 'Bootstrap 4',
    '5' => 'Bootstrap 5',
  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bootstrap_horizontal_tabs_general_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $version = $this->config('bootstrap_horizontal_tabs.settings')->get('version');
    $form['version'] = [
      '#title' => 'Bootstrap library version',
      '#description' => 'Syntax for horizontal tab markup varies between Bootstrap library versions. If tabs are not working as expected with the version of Bootstrap you have integrated, matching the version here may resolve the issue.',
      '#type' => 'radios',
      '#options' => self::$versions,
      '#default_value' => $version ?? 4,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('bootstrap_horizontal_tabs.settings');
    $config->set('version', $form_state->getValue('version'))->save();
    drupal_flush_all_caches();
    parent::submitForm($form, $form_state);
  }

}
