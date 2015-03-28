<?php /**
 * @file
 * Contains \Drupal\new_relic_rpm\EventSubscriber\InitSubscriber.
 */

namespace Drupal\new_relic_rpm\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InitSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [KernelEvents::REQUEST => ['onEvent', 0]];
  }

  public function onEvent() {
    if (variable_get('new_relic_rpm_override_exception_handler', FALSE) && function_exists('newrelic_notice_error')) {
      set_exception_handler('_new_relic_rpm_exception_handler');
    }
  }

}
