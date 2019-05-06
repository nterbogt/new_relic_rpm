<?php

namespace Drupal\Tests\new_relic_rpm\Logger;

use Drupal\Core\Logger\LogMessageParser;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface;
use Drupal\new_relic_rpm\Logger\NewRelicLogger;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;

class NewRelicLoggerTest extends UnitTestCase {

  static $defaultContext = [
    'channel' => 'mytype',
    'ip' => '127.0.0.1',
    'request_uri' => '/foo',
    'referer' => '/bar',
    'uid' => 1,
  ];

  private function getLogger(NewRelicAdapterInterface $adapter, array $levels = []) {
    $parser = new LogMessageParser();
    $config = $this->getConfigFactoryStub([
      'new_relic_rpm.settings' => [
        'watchdog_severities' => $levels,
      ],
    ]);
    return new NewRelicLogger($parser, $adapter, $config);
  }

  public function testLogsSelectedLevelMessages() {
    $adapter = $this->prophesize(NewRelicAdapterInterface::class);
    $adapter
      ->logError(Argument::type('string'))
      ->shouldBeCalled();
    $logger = $this->getLogger($adapter->reveal(), [RfcLogLevel::CRITICAL]);
    $logger->log(RfcLogLevel::CRITICAL, 'Test', self::$defaultContext);
  }

  public function testIgnoresUnselectedLevelMessages() {
    $adapter = $this->prophesize(NewRelicAdapterInterface::class);
    $adapter
      ->logError()
      ->shouldNotBeCalled();
    $logger = $this->getLogger($adapter->reveal());
    $logger->log(RfcLogLevel::CRITICAL, 'Test', self::$defaultContext);
  }

  public function getMessageTests() {

    return [
      ['My Log Message |', self::$defaultContext],
      ['Severity: (2) Critical |', self::$defaultContext],
      ['Type: mytype |', self::$defaultContext],
      ['Request URI: /foo |', self::$defaultContext],
      ['Referrer URI: /bar |', self::$defaultContext],
      ['User: 1', self::$defaultContext],
      ['IP Address: 127.0.0.1', self::$defaultContext],
    ];
  }

  /**
   * @dataProvider getMessageTests
   */
  public function testCreatesMessage($expectedPart, $context) {
    $adapter = $this->prophesize(NewRelicAdapterInterface::class);
    $adapter
      ->logError(Argument::containingString($expectedPart))
      ->shouldBeCalled();

    $logger = $this->getLogger($adapter->reveal(), [RfcLogLevel::CRITICAL]);
    $logger->log(RfcLogLevel::CRITICAL, 'My Log Message', $context);
  }

  public function testHandlesUnknownLevel() {
    $adapter = $this->prophesize(NewRelicAdapterInterface::class);
    $adapter
      ->logError(Argument::containingString('Severity: (8) Unknown'))
      ->shouldBeCalled();

    $logger = $this->getLogger($adapter->reveal(), [8]);
    $logger->log(8, 'My Log Message', self::$defaultContext);
  }

}
