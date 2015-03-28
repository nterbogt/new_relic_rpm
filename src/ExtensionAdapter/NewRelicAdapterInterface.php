<?php

/**
 * @file
 * Contains \Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface.
 */

namespace Drupal\new_relic_rpm\ExtensionAdapter;

/**
 * New relic API adapter interface.
 */
interface NewRelicAdapterInterface {

  const STATE_IGNORE = 'ignore';

  const STATE_BACKGROUND = 'bg';

  /**
   * Set the new relic transaction state.
   *
   * @param string $state
   *   One of the state constants.
   */
  public function setTransactionState($state);

  /**
   * Logs an exception.
   *
   * @param \Exception $e
   *   The exception.
   */
  public function logException(\Exception $e);

  /**
   * Logs an error message.
   *
   * @param string $message
   *   The error message.
   */
  public function logError($message);

  /**
   * Adds a custom parameter.
   *
   * @param string $key
   *   Key that identifies the parameter.
   * @param string $value
   *   Value for the parameter
   */
  public function addCustomParameter($key, $value);

  /**
   * Set the transaction name.
   *
   * @param string $name
   *   Name for this transaction.
   *
   */
  public function setTransactionName($name);

}
