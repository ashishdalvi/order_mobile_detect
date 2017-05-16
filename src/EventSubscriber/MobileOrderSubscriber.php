<?php

namespace Drupal\order_mobile_detect\EventSubscriber;

use Drupal\Component\Datetime\Time;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Detection\MobileDetect;
use Drupal\Core\Database\Connection;

/**
 * Fetches mobile order id.
 */
class MobileOrderSubscriber implements EventSubscriberInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The time object.
   *
   * @var \Drupal\Component\Datetime\Time
   */
  protected $time;

  /**
   * Creates a MobileOrderSubscriber constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Component\Datetime\Time $time
   *   The time object.
   */
  public function __construct(Connection $database, Time $time) {
    $this->database = $database;
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      'commerce_order.place.post_transition' => ['getOrderId'],
    ];
    return $events;
  }

  /**
   * Fetches the order id.
   *
   * Order id is then passed on to detect whether the order is placed using
   * Mobile or Tablet.
   *
   * @param \Drupal\state_machine\Event\WorkflowTransitionEvent $event
   *   The transition event.
   */
  public function getOrderId(WorkflowTransitionEvent $event) {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $event->getEntity();
    $this->detectMobile($order->id());
  }

  /**
   * Detect the mobile.
   *
   * This function fetches all mobile related necessary data to be stored using
   * the mobile detect library.
   *
   * @param $order_id
   *   The order id.
   */
  public function detectMobile($order_id) {
    // Check if order is placed using mobile or tablet.
    $detect = new MobileDetect();

    if ($detect->isMobile() || $detect->isTablet()) {
      // Check if order is placed using windows mobile.
      if ($detect->isWindowsPhoneOS()) {
        $this->insertOrders($order_id, 'Windows', $detect->version('Windows Phone'));
      }
      // Check if order is placed using android mobile.
      elseif ($detect->isAndroidOS()) {
        $this->insertOrders($order_id, 'Android', $detect->version('Android'));
      }
      // Check if order is placed using iOS mobile.
      elseif ($detect->isiOS()) {
        $this->insertOrders($order_id, 'iOS', $detect->version('iPhone'));
      }
      // Check if order is placed using blackberrry mobile.
      elseif ($detect->isBlackBerryOS()) {
        $this->insertOrders($order_id, 'BlackBerry', $detect->version('BlackBerry'));
      }
      // Check if order is placed using symbian mobile.
      elseif ($detect->isSymbianOS()) {
        $this->insertOrders($order_id, 'Symbian', $detect->version('Symbian'));
      }
    }
  }

  /**
   * Insert mobile order data.
   *
   * This function inserts the data related to order placed using mobile in the
   * database table.
   *
   * @param $order_id
   *   The order id.
   * @param $mobile_os
   *   The OS of mobile using which order was placed.
   * @param $mobile_os_version
   *   The OS version of mobile using which order was placed.
   */
  public function insertOrders($order_id, $mobile_os, $mobile_os_version) {
    // Fetch the current user object.
    $user = \Drupal::currentUser();

    // Fetch name of user.
    $user_name = $user->isAnonymous() ? new TranslatableMarkup('Anonymous (not verified)') : $user->getAccountName();

    // Check if order does not exists, then proceed.
    if (!$this->orderExists($order_id)) {
      // Insert detected mobile data into database.
      $this->database->insert('order_mobile_detect')
        ->fields([
          'order_id' => $order_id,
          'user' => $user_name,
          'mobile_os' => $mobile_os,
          'mobile_os_version' => $mobile_os_version,
          'created' => $this->time->getRequestTime(),
        ])
        ->execute();
    }
  }

  /**
   * Check if order already exists or not.
   *
   * @param int|string $order_id
   *   The order id to be checked.
   *
   * @return mixed
   */
  public function orderExists($order_id) {

    $query = $this->database->select('order_mobile_detect', 'omd');
    $query->fields('omd');
    $query->condition('omd.order_id', $order_id);
    $result = $query->execute()->fetchAll();
    return !empty($result) ? true : false;
  }
}
