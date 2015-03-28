<?php /**
 * @file
 * Contains \Drupal\new_relic_rpm\Logger\DefaultLogger.
 */

namespace Drupal\new_relic_rpm\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class DefaultLogger implements LoggerInterface {

  use LoggerTrait;

  /**
   * {@inheritdoc}
   */
  public function log($level, $message, array $context = array()) {
    /**
     * @FIXME
     * Port your hook_watchdog() logic here.
     */
  }

}
