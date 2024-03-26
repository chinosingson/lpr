<?php

namespace Drupal\login_switch\Routing;

use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Config\ConfigFactoryInterface;

class ExceptionEventSubscriber implements EventSubscriberInterface  {

  protected $configFactory;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * ExceptionEventSubscriber constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   */
  public function __construct(ConfigFactoryInterface $config_factory, AccountProxyInterface $current_user) {
    $this->configFactory = $config_factory;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
        KernelEvents::EXCEPTION => ['onException', 100],
    ];
  }

  public function onException(\Symfony\Component\HttpKernel\Event\ExceptionEvent $event) {
    $route_name = \Drupal::routeMatch()->getRouteName();
    $config = $this->configFactory->get('login_switch.settings');
    if ($config->get('login_disabled')) {
      if ($route_name == 'user.page' && !$this->currentUser->hasPermission('access 403 page')) {
        $event->setThrowable(new NotFoundHttpException());
      }
    }
  }

}
