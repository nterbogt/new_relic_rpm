<?php

/**
 * @file
 * Contains \Drupal\new_relic_rpm\Logger\NewRelicLogger.
 */

namespace Drupal\new_relic_rpm\Logger;

use Drupal\Core\Logger\LogMessageParserInterface;
use Drupal\Core\Logger\RfcLogLevel;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class NewRelicLogger implements LoggerInterface {

  use LoggerTrait;

  /**
   * The message's placeholders parser.
   *
   * @var \Drupal\Core\Logger\LogMessageParserInterface
   */
  protected $parser;

  /**
   * Constructs a DbLog object.
   *
   * @param \Drupal\Core\Logger\LogMessageParserInterface $parser
   *   The parser to use when extracting message variables.
   */
  public function __construct(LogMessageParserInterface $parser) {
    $this->parser = $parser;
  }

  /**
   * {@inheritdoc}
   */
  public function log($level, $message, array $context = array()) {
    // Don't do anything if the new relic extension is not available.
    if (!function_exists('newrelic_notice_error')) {
      return;
    }

    $message_placeholders = $this->parser->parseMessagePlaceholders($message, $context);

    // Skip if already logged.
    // @todo
    if (!empty($context['variables']['new_relic_already_logged'])) {
      return;
    }

    // Check if the severity is supposed to be logged.
    if (!in_array($level, \Drupal::config('new_relic_rpm.settings')->get('watchdog_severities'))) {
      return;
    }

    $severity_list = RfcLogLevel::getLevels();

    $message = "@message | Severity: (@severity) @severity_desc | Type: @type | Request URI:  @request_uri | Referrer URI: @referer_uri | User: (@uid) @name | IP Address: @ip";

    $message = strtr($message, array(
      '@severity' => $level,
      '@severity_desc' => $severity_list[$level],
      '@type' => $context['channel'],
      '@ip' => $context['ip'],
      '@request_uri' => $context['request_uri'],
      '@referer_uri' => $context['referer'],
      '@uid' => $context['uid'],
      '@name' => $context['user']->getUsername(),
      '@message' => strip_tags(strtr($context['message'], $message_placeholders)),
    ));

    newrelic_notice_error($message);
  }

}
