<?php

namespace Drupal\new_relic_rpm\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\Render\Element;

/**
 * Provides a settings form to configure the New Relic RPM module.
 */
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
  protected function getEditableConfigNames() {
    return ['new_relic_rpm.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => t('API Key'),
      '#description' => t('Enter your New Relic API key if you wish to view reports and analysis within Drupal.'),
      '#default_value' => \Drupal::config('new_relic_rpm.settings')->get('api_key'),
    ];

    $form['server'] = [
      '#type' => 'details',
      '#title' => t('Server tracking (APM)'),
      '#open' => TRUE,
    ];

    $form['server']['track_drush'] = [
      '#type' => 'select',
      '#title' => $this->t('Drush transactions'),
      '#description' => $this->t('How do you wish New Relic to track drush commands?'),
      '#options' => [
        'ignore' => $this->t('Ignore completely'),
        'bg' => $this->t('Track as background tasks'),
        'norm' => $this->t('Track normally'),
      ],
      '#default_value' => $this->config('new_relic_rpm.settings')->get('track_drush'),
    ];

    $form['server']['track_cron'] = [
      '#type' => 'select',
      '#title' => $this->t('Cron transactions'),
      '#description' => $this->t('How do you wish New Relic to track cron tasks?'),
      '#options' => [
        'ignore' => $this->t('Ignore completely'),
        'bg' => $this->t('Track as background tasks'),
        'norm' => $this->t('Track normally'),
      ],
      '#default_value' => $this->config('new_relic_rpm.settings')->get('track_cron'),
    ];

    $roles = user_role_names();
    $form['server']['ignore_roles'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#title' => $this->t('Ignore Roles'),
      '#description' => $this->t('Select roles that you wish to be ignored on the New Relic dashboards. Any user with at least one of the selected roles will be ignored.'),
      '#options' => $roles,
      '#default_value' => $this->config('new_relic_rpm.settings')->get('ignore_roles'),
    ];

    $form['server']['ignore_urls'] = [
      '#type' => 'textarea',
      '#wysiwyg' => FALSE,
      '#title' => $this->t('Ignore URLs'),
      '#description' => $this->t('Enter URLs you wish New Relic to ignore. Enter one URL per line.'),
      '#default_value' => $this->config('new_relic_rpm.settings')->get('ignore_urls'),
    ];

    $form['server']['bg_urls'] = [
      '#type' => 'textarea',
      '#wysiwyg' => FALSE,
      '#title' => $this->t('Background URLs'),
      '#description' => $this->t('Enter URLs you wish to have tracked as background tasks. Enter one URL per line.'),
      '#default_value' => $this->config('new_relic_rpm.settings')->get('bg_urls'),
    ];

    $form['server']['exclusive_urls'] = [
      '#type' => 'textarea',
      '#wysiwyg' => FALSE,
      '#title' => $this->t('Exclusive URLs'),
      '#description' => $this->t('Enter URLs you wish to exclusively track. This is useful for debugging specific issues. **NOTE** Entering URLs here effectively marks all other URLs as ignored. Leave blank to disable.'),
      '#default_value' => $this->config('new_relic_rpm.settings')->get('exclusive_urls'),
    ];

    $form['views'] = [
      '#type' => 'details',
      '#title' => t('Views tracking'),
      '#open' => TRUE,
    ];

    $form['views']['views_log_slow'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Track slow views'),
      '#description' => $this->t('Check if you want to log slow views in New Relic as custom events.'),
      '#default_value' => $this->config('new_relic_rpm.settings')->get('views_log_slow'),
    ];

    $form['views']['views_log_threshold'] = [
      '#type' => 'number',
      '#title' => $this->t('Slow view threshold'),
      '#field_suffix' => 'ms',
      '#description' => $this->t('The amount of time in milliseconds before a slow view event is logged in New Relic.'),
      '#default_value' => $this->config('new_relic_rpm.settings')->get('views_log_threshold'),
    ];

    $form['error'] = [
      '#type' => 'details',
      '#title' => t('Error tracking'),
      '#open' => TRUE,
    ];

    $form['error']['watchdog_severities'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#title' => $this->t('Forward watchdog messages'),
      '#description' => $this->t('Select which watchdog severities should be forwarded to New Relic API as errors.'),
      '#options' => RfcLogLevel::getLevels(),
      '#default_value' => $this->config('new_relic_rpm.settings')->get('watchdog_severities'),
    ];

    $form['error']['override_exception_handler'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Override exception handler'),
      '#description' => $this->t('Check to override default Drupal exception handler and to have exceptions passed to New Relic.'),
      '#default_value' => $this->config('new_relic_rpm.settings')->get('override_exception_handler'),
    ];

    $form['deployment'] = [
      '#type' => 'details',
      '#title' => t('Deployment tracking'),
      '#open' => TRUE,
    ];

    $form['deployment']['module_deployment'] = [
      '#type' => 'select',
      '#title' => $this->t('Track module activation as deployment'),
      '#description' => $this->t('Turning this on will create a "deployment" on the New Relic dashboard each time a module is installed or uninstalled. This will help you track before and after statistics.'),
      '#options' => [
        1 => $this->t('Enable'),
        0 => $this->t('Disable'),
      ],
      '#default_value' => (int) $this->config('new_relic_rpm.settings')->get('module_deployment'),
    ];

    $form['deployment']['config_import'] = [
      '#type' => 'select',
      '#title' => $this->t('Track configuration imports as deployment'),
      '#description' => $this->t('Turning this on will create a "deployment" on the New Relic dashboard each time a set of configuration is imported. This will help you track before and after statistics.'),
      '#options' => [
        1 => $this->t('Enable'),
        0 => $this->t('Disable'),
      ],
      '#default_value' => (int) $this->config('new_relic_rpm.settings')->get('config_import'),
    ];

    $form['rum'] = [
      '#type' => 'details',
      '#title' => t('Real User Monitoring (RUM)'),
      '#open' => TRUE,
    ];

    $form['rum']['disable_autorum'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable AutoRUM'),
      '#description' => $this->t('Check to disable the automatic real user monitoring inserted by a New Relic transaction.'),
      '#default_value' => $this->config('new_relic_rpm.settings')->get('disable_autorum'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('new_relic_rpm.settings');
    $variables = [
      'api_key',
      'track_drush',
      'track_cron',
      'ignore_roles',
      'ignore_urls',
      'bg_urls',
      'exclusive_urls',
      'watchdog_severities',
      'override_exception_handler',
      'module_deployment',
      'config_import',
      'views_log_slow',
      'views_log_threshold',
      'disable_autorum',
    ];

    foreach ($variables as $variable) {
      $config->set($variable, $form_state->getValue($variable));
    }
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
