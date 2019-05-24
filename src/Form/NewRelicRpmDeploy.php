<?php

namespace Drupal\new_relic_rpm\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;

/**
 * Provides a form to allow marking deployments on the New Relic interface.
 */
class NewRelicRpmDeploy extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'new_relic_rpm_deploy';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];

    $form['revision'] = [
      '#type' => 'textfield',
      '#title' => t('Revision'),
      '#required' => TRUE,
      '#description' => t('Add a revision number to this deployment.'),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => t('Description'),
      '#description' => t('Provide some notes and description regarding this deployment.'),
    ];

    $form['user'] = [
      '#type' => 'textfield',
      '#title' => t('User'),
      '#default_value' => \Drupal::currentUser()->getAccountName(),
      '#description' => t('Enter the name for this deployment of your application. This will be the name shown in your list of deployments on the New Relic RPM website.'),
    ];

    $form['changelog'] = [
      '#type' => 'textarea',
      '#title' => t('Changelog'),
      '#description' => t('Provide a specific changelog for this deployment.'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Create Deployment'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    /** @var \Drupal\new_relic_rpm\Client\NewRelicApiClient $client */
    $client = \Drupal::service('new_relic_rpm.client');
    $deployment = $client->createDeployment(
      $form_state->getValue(['revision']),
      $form_state->getValue(['description']),
      $form_state->getValue(['user']),
      $form_state->getValue(['changelog'])
    );

    if ($deployment) {
      $this->messenger()->addStatus($this->t('New Relic RPM deployment created successfully.'));
    }
    else {
      $this->messenger()->addError($this->t('New Relic RPM deployment failed.'));
    }
  }

}
