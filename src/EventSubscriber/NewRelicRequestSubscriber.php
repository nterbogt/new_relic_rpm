<?php

/**
 * @file
 * Contains \Drupal\new_relic_rpm\EventSubscriber\NewRelicRequestSubscriber.
 */

namespace Drupal\new_relic_rpm\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Request event listener to set transaction name and flag ignore/background.
 */
class NewRelicRequestSubscriber implements EventSubscriberInterface {

  /**
   * New Relic adapter.
   *
   * @var \Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface
   */
  protected $adapter;

  /**
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPathStack;

  /**
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a subscriber.
   *
   * @param \Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface $adapter
   */
  public function __construct(NewRelicAdapterInterface $adapter, PathMatcherInterface $path_matcher, ConfigFactoryInterface $config_factory, CurrentPathStack $current_path_stack, RouteMatchInterface $route_match) {
    $this->adapter = $adapter;
    $this->pathMatcher = $path_matcher;
    $this->config = $config_factory->get('new_relic_rpm.settings');
    $this->routeMatch = $route_match;
    $this->currentPathStack = $current_path_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Run after RouterListener, which has priority 32.
    return [KernelEvents::REQUEST => ['onRequest', 30]];
  }

  /**
   * Set the desired transaction state and name, based on the current path and matched route.
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   */
  public function onRequest(GetResponseEvent $event) {
    if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
      return;
    }

    $ignore_urls = $this->config->get('ignore_urls');
    $bg_urls = $this->config->get('bg_urls');
    $exclude_urls = $this->config->get('exclusive_urls');

    $path = ltrim($this->currentPathStack->getPath(), '/');
    if (!empty($exclude_urls)) {
      if (!$this->pathMatcher->matchPath($path, $exclude_urls)) {
        return $this->adapter->setTransactionState(NewRelicAdapterInterface::STATE_IGNORE);
      }
    }

    if (!empty($ignore_urls)) {
      if ($this->pathMatcher->matchPath($path, $ignore_urls)) {
        return $this->adapter->setTransactionState(NewRelicAdapterInterface::STATE_IGNORE);
      }
    }

    if (!empty($bg_urls)) {
      if ($this->pathMatcher->matchPath($path, $bg_urls)) {
        $this->adapter->setTransactionState(NewRelicAdapterInterface::STATE_BACKGROUND);
      }
    }

    // If the path was not ignored, set the transaction mame.
    // @todo: Make this configurable? New relic currently provides completely
    //   bogus transaction names, what it tries to do is the controller name.
    $this->adapter->setTransactionName($this->routeMatch->getRouteName());
  }

}
