<?php

namespace Drupal\new_relic_rpm\RouteEnhancer;

use Drupal\Core\Controller\ControllerResolverInterface;
use Drupal\Core\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * Enhances routes with a dynamic transaction name.
 */
class TransactionNameEnhancer implements RouteEnhancerInterface {

  private $resolver;

  /**
   * Constructor.
   */
  public function __construct(ControllerResolverInterface $resolver) {
    $this->resolver = $resolver;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(Route $route) {
    return $route->hasDefault('_transaction_name_callback');
  }

  /**
   * {@inheritdoc}
   */
  public function enhance(array $defaults, Request $request) {
    $cb = $defaults['_transaction_name_callback'];
    $callable = $this->resolver->getControllerFromDefinition($cb);
    // Clone the request so we can set the attributes now.  Otherwise,
    // attributes aren't populated until after the route is enhanced.
    $cloned = clone $request;
    $cloned->attributes->replace($defaults);
    $arguments = $this->resolver->getArguments($cloned, $callable);
    $defaults['_transaction_name'] = call_user_func_array($callable, $arguments);
    return $defaults;
  }

}
