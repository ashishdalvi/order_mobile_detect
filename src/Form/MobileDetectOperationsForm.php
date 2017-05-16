<?php

namespace Drupal\order_mobile_detect\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Url;

class MobileDetectOperationsForm extends FormBase {

  /**
   * @inheritdoc
   */
  public function getFormId() {
    return 'mobile_detect_operations_form';
  }

  /**
   * @inheritdoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get query parameters.
    $query_parameters = \Drupal::request()->query->all();

    // Check if query parameters are not empty.
    if ($query_parameters) {
      $order_id = Html::escape($query_parameters['order-id']);
      $mobile_os = Html::escape($query_parameters['mobile-os']);
    }

    $form['order_mobile_detect_order_id'] = [
      '#title' => $this->t('Order number'),
      '#type' => 'textfield',
      '#size' => 10,
      '#default_value' => isset($order_id) && !empty($order_id) ? $order_id : '',
    ];
    // Preparing options for mobile OS.
    $order_mobile_detect_os_options = [
      '' => 'Select',
      'Android' => $this->t('Android'),
      'iOS' => $this->t('iOS'),
      'Windows' => $this->t('Windows'),
      'Symbian' => $this->t('Symbian'),
      'BlackBerry' => $this->t('Blackberry'),
    ];
    $form['order_mobile_detect_mobile_os'] = [
      '#title' => $this->t('Mobile OS'),
      '#type' => 'select',
      '#options' => $order_mobile_detect_os_options,
      '#default_value' => isset($mobile_os) && !empty($mobile_os) ? $mobile_os : '',
    ];
    $form['order_mobile_detect_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
      '#attributes' => [
        'class' => ['form-actions'],
      ],
    ];
    $form['order_mobile_detect_reset'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reset'),
      '#attributes' => [
        'class' => ['form-actions'],
      ],
    ];
    $form['order_mobile_detect_export'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export'),
      '#attributes' => [
        'class' => ['form-actions'],
      ],
    ];
    return $form;
  }

  /**
   * @inheritdoc
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    switch ($form_state->getValue('op')) {
      case 'Filter':
        if (!empty($form_state->getValue('order_mobile_detect_order_id')) && !is_numeric($form_state->getValue('order_mobile_detect_order_id'))) {
          $form_state->setErrorByName('order_mobile_detect_order_id ', $this->t('You have specified an invalid order.'));
        }
        break;
    }
  }

  /**
   * @inheritdoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    switch ($form_state->getValue('op')) {
      case 'Filter':
        $form_state->setRedirectUrl(Url::fromUserInput('?', [
          'query' => [
            'order-id' => $form_state->getValue('order_mobile_detect_order_id'),
            'mobile-os' => $form_state->getValue('order_mobile_detect_mobile_os'),
          ],]));
        break;
      case 'Reset':
        $form_state->setRedirectUrl(Url::fromRoute('order_mobile_detect.mobile_orders_list'));
        break;
      case 'Export':
        $form_state->setRedirectUrl(Url::fromRoute('order_mobile_detect.mobile_orders_export',[], [
          'query' => [
            'order-id' => $form_state->getValue('order_mobile_detect_order_id'),
            'mobile-os' => $form_state->getValue('order_mobile_detect_mobile_os'),
          ],]));
        break;
    }
  }
}
