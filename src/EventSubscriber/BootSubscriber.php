<?php /**
 * @file
 * Contains \Drupal\new_relic_rpm\EventSubscriber\BootSubscriber.
 */

namespace Drupal\new_relic_rpm\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BootSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [KernelEvents::REQUEST => ['onEvent', 0]];
  }

  public function onEvent(\Symfony\Component\HttpKernel\Event\GetResponseEvent $event) {

    $ignore_urls = \Drupal::config('new_relic_rpm.settings')->get('ignore_urls');
    $bg_urls = \Drupal::config('new_relic_rpm.settings')->get('bg_urls');
    $exclu_urls = \Drupal::config('new_relic_rpm.settings')->get('exclusive_urls');

    // Handle cases where this getting called from command line and q isn't set.
    $path = isset($_GET['q']) ? $_GET['q'] : '';

    if (!empty($exclu_urls)) {
      if (!new_relic_rpm_page_match($path, $exclu_urls)) {
        return new_relic_rpm_set_job_state('ignore');
      }
    }

    if (!empty($ignore_urls)) {
      if (new_relic_rpm_page_match($path, $ignore_urls)) {
        return new_relic_rpm_set_job_state('ignore');
      }
    }

    if (!empty($bg_urls)) {
      if (new_relic_rpm_page_match($path, $bg_urls)) {
        return new_relic_rpm_set_job_state('bg');
      }
    }
  }

}
