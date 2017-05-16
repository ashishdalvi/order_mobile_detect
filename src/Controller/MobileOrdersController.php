<?php

namespace Drupal\order_mobile_detect\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Component\Utility\Html;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
<<<<<<< HEAD
use Symfony\Component\HttpFoundation\Response;
=======
>>>>>>> 30b95acd3595162b21e796296d3199e3520429ca

class MobileOrdersController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The date formatter object.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * Creates a MobileOrdersController constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Datetime\DateFormatter $dateFormatter
   *   The date formatter object.
   */
  public function __construct(Connection $database, DateFormatter $dateFormatter) {
    $this->database = $database;
    $this->dateFormatter = $dateFormatter;
  }

  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container) {
    return new static (
      $container->get('database'),
      $container->get('date.formatter')
    );
  }

  /**
   * Display the markup.
   *
   * @return array
   */
  public function orders() {

    // Initialize variables.
    $order_id = '';
    $mobile_os = '';

<<<<<<< HEAD
    // Fetch current path arguments.
=======
>>>>>>> 30b95acd3595162b21e796296d3199e3520429ca
    $current_path = \Drupal::service('path.current')->getPath();
    $path_args = explode('/', $current_path);

    // Render form to help filter output.
    $build['operations_form'] = \Drupal::formBuilder()->getForm('Drupal\order_mobile_detect\Form\MobileDetectOperationsForm');
    // Get query parameters.
<<<<<<< HEAD
=======
    //$query_parameters = $this->request->query->all();
>>>>>>> 30b95acd3595162b21e796296d3199e3520429ca
    $query_parameters = \Drupal::request()->query->all();

    // Check if query parameters are not empty.
    if ($query_parameters) {
      $order_id = Html::escape($query_parameters['order-id']);
      $mobile_os = Html::escape($query_parameters['mobile-os']);
    }
    // The table headers.
    $headers = [
      ['data' => 'Order number', 'field' => 'order_id', 'sort' => 'asc'],
      ['data' => 'Created', 'field' => 'created', 'sort' => 'asc'],
      ['data' => 'User', 'field' => 'user', 'sort' => 'asc'],
      ['data' => 'Mobile OS', 'field' => 'mobile_os', 'sort' => 'asc'],
      ['data' => 'Mobile OS version', 'field' => 'mobile_os_version', 'sort' => 'asc'],
    ];
    // Fetch records from the database table.
    $query = $this->database->select('order_mobile_detect', 'omd');
<<<<<<< HEAD
=======
    //$query = \Drupal::database()->select('order_mobile_detect', 'omd');
>>>>>>> 30b95acd3595162b21e796296d3199e3520429ca
    if ($query_parameters && $order_id) {
      $query->condition('omd.order_id', $order_id);
    }
    if ($query_parameters && $mobile_os) {
      $query->condition('omd.mobile_os', $mobile_os);
    }
    $query->fields('omd');
<<<<<<< HEAD
    if (Html::escape($path_args[5]) !== 'export') {
=======
    if (Html::escape($path_args[4]) !== 'export') {
>>>>>>> 30b95acd3595162b21e796296d3199e3520429ca
      $query->extend('Drupal\Core\Database\Query\TableSortExtender')
        ->orderByHeader($headers);
      $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')
        ->limit(50);
      $result = $query->execute()->fetchAll();
    }
    else {
      $result = $query->execute()->fetchAll();
    }
    if (empty($result)) {
<<<<<<< HEAD
      $build['mobile_orders'] = [
        '#markup' => $this->t('No orders found.')
      ];
=======
      dpr('hi empty');exit;
      $build['mobile_orders'] = $this->t('No orders found.');
>>>>>>> 30b95acd3595162b21e796296d3199e3520429ca
    }
    else {
      // The table rows.
      $rows = [];
      foreach ($result as $key => $value) {
<<<<<<< HEAD
        if (Html::escape($path_args[5]) !== 'export') {
          $user_load = user_load_by_name($value->user);
          if ($value->user !== 'Anonymous (not verified)') {
            $rows[] = [
              Link::fromTextAndUrl($value->order_id, Url::fromUri('internal:/admin/commerce/orders/' . $value->order_id)),
              $this->dateFormatter->format($value->created, 'custom', 'd-m-Y H:i:s'),
              Link::fromTextAndUrl($value->user, Url::fromUri('internal:/user/' . $user_load->id())),
=======
        if (Html::escape($path_args[4]) !== 'export') {
          $user_load = user_load_by_name($value->user);
          if ($value->user !== 'Anonymous (not verified)') {
            $rows[] = [
              Link::fromTextAndUrl($value->order_id, Url::fromUri('/admin/commerce/orders/' . $value->order_id)),
              $this->dateFormatter->format($value->created, 'custom', 'd-m-Y H:i:s'),
              //date('d-m-Y H:i:s', $value->created),
              Link::fromTextAndUrl($value->user, Url::fromUri('/user/' . $user_load->uid)),
>>>>>>> 30b95acd3595162b21e796296d3199e3520429ca
              $value->mobile_os,
              $value->mobile_os_version
            ];
          }
          else {
            $rows[] = [
<<<<<<< HEAD
              Link::fromTextAndUrl($value->order_id, Url::fromUri('internal:/admin/commerce/orders/' . $value->order_id)),
              $this->dateFormatter->format($value->created, 'custom', 'd-m-Y H:i:s'),
=======
              Link::fromTextAndUrl($value->order_id, Url::fromUri('/admin/commerce/orders/' . $value->order_id)),
              $this->dateFormatter->format($value->created, 'custom', 'd-m-Y H:i:s'),
              //date('d-m-Y H:i:s', $value->created),
>>>>>>> 30b95acd3595162b21e796296d3199e3520429ca
              $value->user,
              $value->mobile_os,
              $value->mobile_os_version
            ];
          }
        }
        else {
          $rows[] = [
            $value->order_id,
            $this->dateFormatter->format($value->created, 'custom', 'd-m-Y H:i:s'),
<<<<<<< HEAD
=======
            //date('d-m-Y H:i:s', $value->created),
>>>>>>> 30b95acd3595162b21e796296d3199e3520429ca
            $value->user,
            $value->mobile_os,
            $value->mobile_os_version
          ];
        }
      }
<<<<<<< HEAD
      if (Html::escape($path_args[5]) == 'export') {

        $headers = ['Order number', 'Created', 'User', 'Mobile OS', 'Mobile OS version'];

        // Export as CSV.
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=Mobile-Orders.csv;');
        $response->sendHeaders();

=======
      if (Html::escape($path_args[4]) !== 'export') {dpr('hi');exit;
        // Render theme table.
        $build['mobile_orders'] = [
          '#theme' => 'table',
          '#rows' => $rows,
          '#header' => $headers
        ];
        $build['pager'] = ['#type' => 'pager'];
      }
      else {
        $headers = array('Order number', 'Created', 'User', 'Mobile OS', 'Mobile OS version');
        // Export as CSV.
        $filename = 'Mobile-Orders.csv';
        \Drupal::request()->headers->set('Content-Type', 'text/csv; utf-8');
        \Drupal::request()->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        //drupal_add_http_header('Content-Type', 'text/csv; utf-8');
        //drupal_add_http_header('Content-Disposition', 'attachment; filename=' . $filename);
>>>>>>> 30b95acd3595162b21e796296d3199e3520429ca
        $export_output = fopen('php://output', 'w');
        fputcsv($export_output, $headers);
        foreach ($rows as $value) {
          fputcsv($export_output, $value);
        }
        fclose($export_output);
        exit();
      }
<<<<<<< HEAD
      else {
        // Render theme table.
        $build['mobile_orders'] = [
          '#theme' => 'table',
          '#rows' => $rows,
          '#header' => $headers
        ];
        $build['pager'] = ['#type' => 'pager'];
      }
=======
>>>>>>> 30b95acd3595162b21e796296d3199e3520429ca
    }
    return $build;
  }
}
