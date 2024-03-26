<?php

namespace Drupal\login_switch\Routing;

use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteSubscriberBase;

/**
 * Route subscriber class to override the default core user routes.
 */
class LoginSwitchRouteSubscriber extends RouteSubscriberBase {

  protected $configFactory;

  /**
   * LoginSwitchRouteSubscriber constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   ConfigFactoryInterface.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Checks if the module is activated and sets routes.
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Get configuration for the module.
    $config = $this->configFactory->get('login_switch.settings');

    $routes = [
      'login' => 'user.login',
      'register' => 'user.register',
      'password' => 'user.pass',
    ];

    foreach ($routes as $key => $route_name) {
      $route = $collection->get($route_name);
      if ($config->get($key . '_disabled') && $route) {
        $route_path = $config->get($key . '_route');

        if (!empty($route_path)) {
          // Set the new path based on the configuration.
          $route->setPath('/' . $route_path);
        }
        else {
          $route->setRequirement('_access', 'false');
        }
      }
    }
  }

}
