<?php

namespace Drupal\Tests\new_relic_rpm\RouteEnhancer;

use Drupal\Core\Controller\ControllerResolverInterface;
use Drupal\new_relic_rpm\RouteEnhancer\TransactionNameEnhancer;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class TransactionNameEnhancerTest extends UnitTestCase {

  public function getAppliesTests() {
    return [
      [FALSE, new Route('/foo')],
      [TRUE, new Route('/foo', ['_transaction_name_callback' => TRUE])]
    ];
  }

  /**
   * @dataProvider getAppliesTests
   */
  public function testApplies($expected, Route $route) {
    $resolver = $this->prophesize(ControllerResolverInterface::class);
    $enhancer = new TransactionNameEnhancer($resolver->reveal());
    $this->assertEquals($expected, $enhancer->applies($route));
  }

  public function testResolvesName() {
    $request = new Request();
    $cb = function() { return 'foo_resolved'; };
    $resolver = $this->prophesize(ControllerResolverInterface::class);
    $resolver->getControllerFromDefinition($cb)->willReturn($cb);
    $resolver->getArguments(Argument::type(Request::class), $cb)
      ->willReturn([]);

    $enhancer = new TransactionNameEnhancer($resolver->reveal());

    $defaults = ['_transaction_name_callback' => $cb];

    $defaults = $enhancer->enhance($defaults, $request);
    $this->assertEquals('foo_resolved', $defaults['_transaction_name']);
  }
}