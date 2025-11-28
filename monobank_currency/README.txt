MONOBANK CURRENCY CONVERTER
===========================

CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Features
 * Troubleshooting
 * Maintainers


INTRODUCTION
------------

The Monobank Currency Converter module provides real-time currency exchange
functionality in Drupal 11 using the official public Monobank API.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/monobank_currency

 * To submit bug reports and feature suggestions, or track changes:
   https://www.drupal.org/project/issues/monobank_currency


REQUIREMENTS
------------

This module requires:
 * Drupal 11.x
 * PHP 8.1 or higher
 * GuzzleHTTP (included in Drupal core)
 * Cron configured to run at least every 30 minutes


INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit
   https://www.drupal.org/node/1897420 for further information.

 * Enable the module at Administration > Extend

 * Configure cron to run every 30 minutes for optimal currency updates


CONFIGURATION
-------------

 * Navigate to Administration > Configuration > Services > Monobank Currency
   to access the settings page.

 * The module works out of the box without configuration. Currency rates are
   fetched automatically via cron.

 * Use the "Clear Cache & Fetch Now" button to immediately fetch the latest
   rates.


FEATURES
--------

✓ Currency Converter Page
  - Accessible at /currency
  - Two conversion modes:
    a) Convert From: Specify source currency and amount
    b) Convert To: Specify target currency and desired amount

✓ Dynamic Currency Rates Table
  - Displays current buy/sell/cross rates for all currencies
  - Highlights selected currencies
  - Real-time search and filtering

✓ Monobank API Integration
  - Fetches rates from https://api.monobank.ua/bank/currency
  - Supports 100+ currencies
  - ISO 4217 currency code mapping

✓ Automatic Cron Updates
  - Fetches rates every 30 minutes via cron
  - Caches rates locally for performance
  - Fallback to cached rates if API unavailable

✓ Currency Flags
  - Visual flags for all currencies using flagcdn.com

✓ Multilingual Support
  - German (de)
  - Ukrainian (uk)
  - Spanish (es)


TROUBLESHOOTING
---------------

 * If currency rates are not updating:
   - Ensure cron is configured and running
   - Check the logs at Administration > Reports > Recent log messages
   - Manually fetch rates from the settings page

 * If the /currency page returns 404:
   - Clear caches at Administration > Configuration > Performance
   - Rebuild routes: drush cache:rebuild

 * If conversion results are incorrect:
   - Check that rates are cached (visit settings page)
   - Some currency pairs may not be available in the Monobank API


MAINTAINERS
-----------

Current maintainers:
 * Monobank Currency Team

This project has been sponsored by:
 * Monobank Ukraine
