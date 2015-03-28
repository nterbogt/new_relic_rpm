<?php

/**
 * @file
 * Contains \Drupal\new_relic_rpm\Form\NewRelicRpmSettings.
 */

namespace Drupal\new_relic_rpm\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class NewRelicRpmSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'new_relic_rpm_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('new_relic_rpm.settings');

    foreach (Element::children($form) as $variable) {
      $config->set($variable, $form_state->getValue($form[$variable]['#parents']));
    }
    $config->save();

    if (method_exists($this, '_submitForm')) {
      $this->_submitForm($form, $form_state);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['new_relic_rpm.settings'];
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface &$form_state) {
    $form = [];

    $form['new_relic_rpm_track_drush'] = [
      '#type' => 'select',
      '#title' => t('Drush transactions'),
      '#description' => t('How do you wish RPM to track drush commands?'),
      '#options' => [
        'ignore' => t('Ignore completely'),
        'bg' => t('Track as background tasks'),
        'norm' => t('Track normally'),
      ],
      '#default_value' => \Drupal::config('new_relic_rpm.settings')->get('new_relic_rpm_track_drush'),
    ];

    $form['new_relic_rpm_track_cron'] = [
      '#type' => 'select',
      '#title' => t('Cron transactions'),
      '#description' => t('How do you wish RPM to track cron tasks?'),
      '#options' => [
        'ignore' => t('Ignore completely'),
        'bg' => t('Track as background tasks'),
        'norm' => t('Track normally'),
      ],
      '#default_value' => \Drupal::config('new_relic_rpm.settings')->get('new_relic_rpm_track_cron'),
    ];

    $form['new_relic_rpm_module_deployment'] = [
      '#type' => 'select',
      '#title' => t('Track module activation as deployment'),
      '#description' => t('Turning this on will create a "deployment" on the New Relic RPM dashboard each time a module is enabled or disabled. This will help you track before and after statistics.'),
      '#options' => [
        '1' => t('Enable'),
        '0' => t('Disable'),
      ],
      '#default_value' => \Drupal::config('new_relic_rpm.settings')->get('new_relic_rpm_module_deployment'),
    ];

    $form['new_relic_rpm_ignore_urls'] = [
      '#type' => 'textarea',
      '#wysiwyg' => FALSE,
      '#title' => t('Ignore URLs'),
      '#description' => t('Enter URLs you wish New Relic RPM to ignore. Enter one URL per line.'),
      '#default_value' => \Drupal::config('new_relic_rpm.settings')->get('new_relic_rpm_ignore_urls'),
    ];

    $form['new_relic_rpm_bg_urls'] = [
      '#type' => 'textarea',
      '#wysiwyg' => FALSE,
      '#title' => t('Background URLs'),
      '#description' => t('Enter URLs you wish to have tracked as background tasks. Enter one URL per line.'),
      '#default_value' => \Drupal::config('new_relic_rpm.settings')->get('new_relic_rpm_bg_urls'),
    ];

    $form['new_relic_rpm_exclusive_urls'] = [
      '#type' => 'textarea',
      '#wysiwyg' => FALSE,
      '#title' => t('Exclusive URLs'),
      '#description' => t('Enter URLs you wish exclusively track. This is usefull for debugging specific issues. **NOTE** Entering URLs here effectively marks all other URLs as ignored. Leave blank to disable.'),
      '#default_value' => \Drupal::config('new_relic_rpm.settings')->get('new_relic_rpm_exclusive_urls'),
    ];

    $form['new_relic_rpm_api_key'] = [
      '#type' => 'textfield',
      '#title' => t('API Key'),
      '#description' => t('Enter your New Relic API key if you wish to view reports and analysis within Drupal'),
      '#default_value' => \Drupal::config('new_relic_rpm.settings')->get('new_relic_rpm_api_key'),
    ];

    // @FIXME
    // Could not extract the default value because it is either indeterminate, or
    // not scalar. You'll need to provide a default value in
    // config/install/new_relic_rpm.settings.yml and config/schema/new_relic_rpm.schema.yml.
    $form['new_relic_rpm_watchdog_severities'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#title' => t('Forward watchdog messages'),
      '#description' => t('Select which watchdog severities should be forwarded to New Relic API as errors.'),
      '#options' => watchdog_severity_levels(),
      '#default_value' => \Drupal::config('new_relic_rpm.settings')->get('new_relic_rpm_watchdog_severities'),
    ];

    $form['new_relic_rpm_override_exception_handler'] = [
      '#type' => 'checkbox',
      '#title' => t('Override exception handler'),
      '#description' => t('Check to override default Drupal exception handler and to have exceptions passed to New Relic'),
      '#default_value' => \Drupal::config('new_relic_rpm.settings')->get('new_relic_rpm_override_exception_handler'),
    ];

    return parent::buildForm($form, $form_state);
  }

}
