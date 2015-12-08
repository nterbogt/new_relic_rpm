<?php

/**
 * @file
 * Contains \Drupal\new_relic_rpm\Timer.
 */

namespace Drupal\new_relic_rpm;

/**
 * Provides helpers to use timers throughout a request.
 */
class Timer {

  static protected $timers = array();

  /**
   * Starts the timer with the specified name.
   *
   * @param $name
   *   The name of the timer.
   */
  static public function start($name) {
    static::$timers[$name] = microtime(TRUE);
  }

  /**
   * Stops the timer with the specified name and returns the time.
   *
   * @param string $name
   *   The name of the timer.
   *
   * @return int
   *   The time since it was started in ms.
   */
  static public function stop($name) {
    if (isset(static::$timers[$name])) {
      $stop = microtime(TRUE);
      $diff = round(($stop - static::$timers[$name]) * 1000, 2);
      return $diff;
    }
  }

}
