<?php
/**
 * @file
 * New Relic RPM Drush integration.
 */

/**
 * Implements hook_drush_init().
 */
function new_relic_rpm_drush_init() {
  if (\Drupal::hasService('new_relic_rpm.adapter')) {
    /** @var \Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface $adapter */
    $adapter = \Drupal::service('new_relic_rpm.adapter');

    // Add custom paramter, telling the drush command executed.
    $command = drush_get_command();
    $drush_command = array_merge(array($command['command']), $command['arguments']);
    $adapter->addCustomParameter('Drush command', implode(' ', $drush_command));

    // Set job state.
    $adapter->setTransactionState(\Drupal::config('new_relic_rpm.settings')->get('track_drush'));
  }
}
