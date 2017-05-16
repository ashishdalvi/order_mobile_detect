CONTENTS OF THIS FILE
---------------------
 * Introduction
 * Requirements
 * Installation
 * Limitations
 * FAQ
 * Maintainers

INTRODUCTION
------------
 * This module tracks the orders placed using mobile(responsive site) and
   displays them in a tabular format.The module also provides a feature to
   export the data in csv format.


REQUIREMENTS
------------
This module requires the following modules:

 * Drupal Commerce(https://www.drupal.org/project/commerce)


INSTALLATION
------------
 * This module needs to be installed via Composer, which will download all
   its dependent modules along-with the required libraries.

   1. Add the Drupal Packagist repository

       ```sh
       composer config repositories.drupal composer https://packages.drupal.org/8
       ```
   This allows Composer to find Order Mobile Detect and the other Drupal modules.

   2. Download Order Mobile Detect

      ```sh
      composer require "drupal/order_mobile_detect ~1.0"
      ```
   This will download the latest release of Order Mobile Detect.


LIMITATIONS
------------
 * Orders placed using mobile app won't be tracked.The module works only for
   orders placed from responsive site.
 * Android,iOS,Blackberry,Windows and Symbian are the only supported OS.
 * No details regarding orders placed from browser will be displayed.


FAQ
------------
 Q. Where can I see all the orders and the mobile OS related data?
 A. You can find all the data under Commerce >> Orders >> Mobile Orders.


MAINTAINER
-----------
Current maintainers:
 * Hardik Pandya (hardik.p) - https://www.drupal.org/user/3220495
