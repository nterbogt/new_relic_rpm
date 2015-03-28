<?php

/**
 * @file
 * Contains \Drupal\new_relic_rpm\EventSubscriber\ExceptionSubscriber.
 */

namespace Drupal\new_relic_rpm\EventSubscriber;

use Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExceptionSubscriber implements EventSubscriberInterface {

  /**
   * New Relic adapter.
   *
   * @var \Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface
   */
  protected $adapter;

  /**
   * Constructs a subscriber.
   *
   * @param \Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface $adapter
   */
  public function __construct(NewRelicAdapterInterface $adapter) {
    $this->adapter = $adapter;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::EXCEPTION][] = ['onException', -256];
    return $events;
  }

  /**
   * Handles errors for this subscriber.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
   *   The event to process.
   */
  public function onException(GetResponseForExceptionEvent $event) {
    // Don't log http exceptions.
    if ($event->getException() instanceof HttpExceptionInterface) {
      return;
    }
    if (\Drupal::config('new_relic_rpm.settings')->get('override_exception_handler')) {
      // Forward the exception to New Relic.
      $this->adapter->logException($event->getException());
    }
  }

}
