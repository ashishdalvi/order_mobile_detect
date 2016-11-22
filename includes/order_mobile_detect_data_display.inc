<?php

/**
 * @file
 * Display orders placed by mobile in a tabular format.
 * 
 * The file includes functions to display mobile orders and mobile OS data.
 */

/**
 * Menu callback; display orders placed by mobile in a tabular format.
 * 
 * @return
 * Mobile order data in a table structure.
 */
function order_mobile_detect_data_display() {
  // Render form to help filter output.
  $order_mobile_detect_form = drupal_get_form('order_mobile_detect_form');
  $output = drupal_render($order_mobile_detect_form);
  // Get query parameters.
  $query_parameters = drupal_get_query_parameters();
  if ($query_parameters) {
    $order_id = check_plain($query_parameters['order-id']);
    $mobile_os = check_plain($query_parameters['mobile-os']);
  }
  // The table headers.
  $headers = array(
    array('data' => 'Order number', 'field' => 'order_id', 'sort' => 'ASC'),
    array('data' => 'Created', 'field' => 'created', 'sort' => 'ASC'),
    array('data' => 'User', 'field' => 'user', 'sort' => 'ASC'),
    array('data' => 'Mobile OS', 'field' => 'mobile_os', 'sort' => 'ASC'),
    array('data' => 'Mobile OS version', 'field' => 'mobile_os_version', 'sort' => 'ASC'),
  );
  // Fetch records from the database table.
  $query = db_select('order_mobile_detect', 'omd');
  if ($query_parameters && $order_id) {
    $query->condition('omd.order_id', $order_id);
  }
  if ($query_parameters && $mobile_os) {
    $query->condition('omd.mobile_os', $mobile_os);
  }
  $query->fields('omd');
  if (check_plain(arg(4)) !== 'export') {
    $result = $query->extend('TableSort')->extend('PagerDefault')->limit(50)->orderByHeader($headers)->execute()->fetchAll();
  }
  else {
    $result = $query->execute()->fetchAll();
  }
  if (empty($result)) {
    $output .= t('No orders found.');
  }
  else {
    // The table rows.
    $rows = array();
    foreach ($result as $key => $value) {
      if (check_plain(arg(4)) !== 'export') {
        $user_load = user_load_by_name($value->user);
        if ($value->user !== 'Anonymous (not verified)') {
          $rows[] = array(l($value->order_id, 'admin/commerce/orders/' . $value->order_id), date('d-m-Y H:i:s', $value->created), l($value->user, 'user/' . $user_load->uid), $value->mobile_os, $value->mobile_os_version);
        }
        else {
          $rows[] = array(l($value->order_id, 'admin/commerce/orders/' . $value->order_id), date('d-m-Y H:i:s', $value->created), $value->user, $value->mobile_os, $value->mobile_os_version);
        }
      }
      else {
        $rows[] = array($value->order_id, date('d-m-Y H:i:s', $value->created), $value->user, $value->mobile_os, $value->mobile_os_version);
      }
    }
    if (check_plain(arg(4)) !== 'export') {
      // Render theme table.
      $output .= theme('table', array('header' => $headers, 'rows' => $rows));
      $output .= theme('pager');
    }
    else {
      $headers = array('Order number', 'Created', 'User', 'Mobile OS', 'Mobile OS version');
      // Export as CSV.
      $filename = 'Mobile-Orders.csv';
      drupal_add_http_header('Content-Type', 'text/csv; utf-8');
      drupal_add_http_header('Content-Disposition', 'attachment; filename=' . $filename);
      $export_output = fopen('php://output', 'w');
      fputcsv($export_output, $headers);
      foreach ($rows as $value) {
        fputcsv($export_output, $value);
      }
      fclose($export_output);
      exit();
    }
  }
  return $output;
}

/**
 * Builds the mobile orders form.
 */
function order_mobile_detect_form($form, &$form_state) {
  // Get query parameters.
  $query_parameters = drupal_get_query_parameters();
  if ($query_parameters) {
    $order_id = check_plain($query_parameters['order-id']);
    $mobile_os = check_plain($query_parameters['mobile-os']);
  }
  $form['order_mobile_detect_order_id'] = array(
    '#title' => t('Order number'),
    '#type' => 'textfield',
    '#size' => 10,
    '#default_value' => isset($order_id) ? $order_id : '',
  );
  // Preparing options for mobile OS.
  $order_mobile_detect_os_options = array(
    '' => 'Select',
    'Android' => t('Android'),
    'iOS' => t('iOS'),
    'Windows' => t('Windows'),
    'Symbian' => t('Symbian'),
    'BlackBerry' => t('Blackberry'),
  );
  $form['order_mobile_detect_mobile_os'] = array(
    '#title' => t('Mobile OS'),
    '#type' => 'select',
    '#options' => $order_mobile_detect_os_options,
    '#default_value' => isset($mobile_os) ? $mobile_os : '',
  );
  $form['order_mobile_detect_submit'] = array(
    '#type' => 'submit',
    '#value' => t('Filter'),
  );
  $form['order_mobile_detect_reset'] = array(
    '#type' => 'submit',
    '#value' => t('Reset'),
  );
  $form['order_mobile_detect_export'] = array(
    '#type' => 'submit',
    '#value' => t('Export'),
  );
  return $form;
}

/**
 * Validate the order mobile detect form.
 */
function order_mobile_detect_form_validate($form, &$form_state) {
  switch ($form_state['values']['op']) {
    case 'Filter':
      if (!empty($form_state['values']['order_mobile_detect_order_id']) && !is_numeric($form_state['values']['order_mobile_detect_order_id'])) {
        form_set_error('order_mobile_detect_order_id ', t('You have specified an invalid order.'));
      }
      break;
  }
}

/**
 * Submit the order mobile detect form.
 */
function order_mobile_detect_form_submit($form, &$form_state) {
  switch ($form_state['values']['op']) {
    case 'Filter':
      drupal_goto('admin/commerce/orders/mobile-orders', array(
        'query' => array(
          'order-id' => $form_state['values']['order_mobile_detect_order_id'],
          'mobile-os' => $form_state['values']['order_mobile_detect_mobile_os'],
        ),));
      break;
    case 'Reset':
      drupal_goto('admin/commerce/orders/mobile-orders');
      break;
    case 'Export':
      drupal_goto('admin/commerce/orders/mobile-orders/export', array(
        'query' => array(
          'order-id' => $form_state['values']['order_mobile_detect_order_id'],
          'mobile-os' => $form_state['values']['order_mobile_detect_mobile_os'],
        ),));
      break;
  }
}

/**
 * Checks whether order id already exists.
 * 
 * This function checks whether the record for the specified order id already
 * exists.
 * 
 * @param $order_id
 * The order id.
 * 
 * @return
 * Table records associated to the order id.
 */
function _order_mobile_detect_check_order_exists($order_id) {
  $query = db_select('order_mobile_detect', 'omd');
  $query->condition('omd.order_id', $order_id);
  $query->fields('omd');
  $result = $query->execute()->rowCount();
  return $result;
}
