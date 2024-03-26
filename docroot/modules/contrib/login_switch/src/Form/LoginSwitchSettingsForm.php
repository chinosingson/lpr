<?php

namespace Drupal\login_switch\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\ProxyClass\Routing\RouteBuilder;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Configure site information settings for this site.
 */
class LoginSwitchSettingsForm extends ConfigFormBase {

  const CONFIG_NAME = 'login_switch.settings';

  /**
   * @var \Drupal\Core\ProxyClass\Routing\RouteBuilder.
   */
  protected $routeBuilder;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'login_switch_settings_form';
  }

  /**
   * Class constructor.
   */
  public function __construct(RouteBuilder $route_builder) {
    $this->routeBuilder = $route_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load the service required to construct this class.
      $container->get('router.builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return [self::CONFIG_NAME];
  }

  /**
   * Returns this modules configuration object.
   */
  protected function getConfig() {
    return $this->config(self::CONFIG_NAME);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getConfig();

    $routes = [
      'login' => $this->t('Login'),
      'register' => $this->t('Registration'),
      'password' => $this->t('Reset password'),
    ];

    foreach ($routes as $key => $label) {
      $form[$key . '_settings'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('@label route settings', ['@label' => $label,]),
      ];
      $form[$key . '_settings'][$key . '_disabled'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Disable default user/@key route', ['@key' => $key,]),
        '#description' => $this->t('This setting will force the user/@key route to be disabled or updated.', ['@key' => $key,]),
        '#default_value' => $config->get($key . '_disabled'),
      ];
      $form[$key . '_settings'][$key . '_route'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Replace user/@key with:', ['@key' => $key,]),
        '#description' => $this->t('Leave empty to make the route completely disabled.'),
        '#default_value' => $config->get($key . '_route'),
        '#required' => FALSE,
        '#states' => [
          'disabled' => [
            ':input[name="' . $key . '_disabled"]' => ['checked' => FALSE],
          ],
        ],
      ];
      $form[$key . '_settings'][$key . '_noindex'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Set user/@key route to noindex', ['@key' => $key,]),
        '#description' => $this->t('This setting will add a header on the user/@key route to be noindex.', ['@key' => $key,]),
        '#default_value' => $config->get($key . '_noindex'),
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->getConfig();
    $values = $form_state->cleanValues()->getValues();
    foreach ($values as $key => $value) {
      if (strpos($key, 'route') !== FALSE) {
        $value = trim($value, '/');
      }
      $config->set($key, $value);
    }
    $config->save();

    $this->routeBuilder->rebuild();

    $this->messenger()->addMessage('Configuration changes were successfully saved.');
  }

}
