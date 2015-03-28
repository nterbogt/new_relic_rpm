<?php

/**
 * @file
 * Contains \Drupal\new_relic_rpm\EventSubscriber\ExceptionSubscriber.
 */

namespace Drupal\new_relic_rpm\EventSubscriber;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExceptionSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::EXCEPTION][] = ['onException', -256];
  }

  /**
   * Handles errors for this subscriber.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
   *   The event to process.
   */
  public function onException(GetResponseForExceptionEvent $event) {
    if (variable_get('new_relic_rpm_override_exception_handler', FALSE) && function_exists('newrelic_notice_error')) {
      // Don't log http exceptions.
      if ($event->getException() instanceof HttpExceptionInterface) {
        return;
      }
      // Forward the exception to New Relic.
      newrelic_notice_error(NULL, $event->getException());
    }
  }

}
