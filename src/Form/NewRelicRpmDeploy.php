<?php

/**
 * @file
 * Contains \Drupal\new_relic_rpm\Form\NewRelicRpmDeploy.
 */

namespace Drupal\new_relic_rpm\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class NewRelicRpmDeploy extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'new_relic_rpm_deploy';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = [];

    $form['deploy_user'] = [
      '#type' => 'textfield',
      '#title' => t('Deployer/Deployment Name'),
      '#required' => TRUE,
      '#description' => t('Enter the name for this deployment of your application. This will be the name shown in your list of deployments on the New Relic RPM website.'),
    ];

    $form['deploy_description'] = [
      '#type' => 'textarea',
      '#title' => t('Deployment Description'),
      '#description' => t('Provide some notes and description regarding this deployment.'),
    ];

    $form['deploy_changelog'] = [
      '#type' => 'textarea',
      '#title' => t('Deployment Changelog'),
      '#description' => t('Provide a specific changelog for this deployment.'),
    ];

    $form['deploy_revision'] = [
      '#type' => 'textfield',
      '#title' => t('Deployment Revision'),
      '#description' => t('Add a revision number to this deployment.'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Create Deployment'),
    ];

    return $form;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {

    $deployments = _new_relic_rpm_deploy($form_state->getValue(['deploy_user']), $form_state->getValue(['deploy_description']), $form_state->getValue(['deploy_changelog']), $form_state->getValue(['deploy_revision']));

    if (strlen($deployments) > 20) {
      drupal_set_message(t('New Relic RPM deployment created successfully'), 'status');
    }
    else {
      // @FIXME
// url() expects a route name or an external URI.
// drupal_set_message(t('New Relic RPM deployment failed to be created. Please ensure you have your account configured on the <a href="@settings">New Relic RPM Drupal admin page</a>.', array('@settings' => url('admin/config/development/new-relic-rpm'))), 'error');

    }
  }

}
