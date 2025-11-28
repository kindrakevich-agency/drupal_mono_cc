<?php

namespace Drupal\monobank_currency\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Component\Datetime\TimeInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Service for fetching and managing currency rates from Monobank API.
 */
class MonobankCurrencyService {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Monobank API endpoint.
   *
   * @var string
   */
  const API_URL = 'https://api.monobank.ua/bank/currency';

  /**
   * Cache ID for currency rates.
   *
   * @var string
   */
  const CACHE_ID = 'monobank_currency_rates';

  /**
   * Cache duration (30 minutes in seconds).
   *
   * @var int
   */
  const CACHE_DURATION = 1800;

  /**
   * Constructs a MonobankCurrencyService object.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(
    ClientInterface $http_client,
    CacheBackendInterface $cache,
    LoggerChannelFactoryInterface $logger_factory,
    TimeInterface $time
  ) {
    $this->httpClient = $http_client;
    $this->cache = $cache;
    $this->logger = $logger_factory->get('monobank_currency');
    $this->time = $time;
  }

  /**
   * Fetch currency rates from Monobank API or cache.
   *
   * @param bool $force_refresh
   *   Force refresh from API, bypassing cache.
   *
   * @return array
   *   Array of currency rates.
   */
  public function getRates($force_refresh = FALSE) {
    // Try to get from cache first.
    if (!$force_refresh) {
      $cached = $this->cache->get(self::CACHE_ID);
      if ($cached && !empty($cached->data)) {
        return $cached->data;
      }
    }

    // Fetch from API.
    $rates = $this->fetchFromApi();

    // Cache the results.
    if (!empty($rates)) {
      $expire = $this->time->getRequestTime() + self::CACHE_DURATION;
      $this->cache->set(self::CACHE_ID, $rates, $expire);
    }

    return $rates;
  }

  /**
   * Fetch currency rates from Monobank API.
   *
   * @return array
   *   Array of currency rates or empty array on failure.
   */
  protected function fetchFromApi() {
    try {
      $response = $this->httpClient->request('GET', self::API_URL, [
        'timeout' => 10,
        'headers' => [
          'Accept' => 'application/json',
        ],
      ]);

      $data = json_decode($response->getBody()->getContents(), TRUE);

      if (json_last_error() !== JSON_ERROR_NONE) {
        $this->logger->error('Failed to parse Monobank API response: @error', [
          '@error' => json_last_error_msg(),
        ]);
        return [];
      }

      $this->logger->info('Successfully fetched currency rates from Monobank API. Total rates: @count', [
        '@count' => count($data),
      ]);

      return $data;
    }
    catch (RequestException $e) {
      $this->logger->error('Failed to fetch currency rates from Monobank API: @message', [
        '@message' => $e->getMessage(),
      ]);

      // Try to return cached data even if expired.
      $cached = $this->cache->get(self::CACHE_ID);
      if ($cached && !empty($cached->data)) {
        $this->logger->notice('Using expired cached currency rates as fallback.');
        return $cached->data;
      }

      return [];
    }
  }

  /**
   * Get exchange rate between two currencies.
   *
   * @param int $from_code
   *   Source currency numeric code.
   * @param int $to_code
   *   Target currency numeric code.
   * @param string $type
   *   Rate type: 'buy', 'sell', or 'cross'.
   *
   * @return float|null
   *   Exchange rate or NULL if not found.
   */
  public function getRate($from_code, $to_code, $type = 'buy') {
    $rates = $this->getRates();

    foreach ($rates as $rate) {
      if ($rate['currencyCodeA'] == $from_code && $rate['currencyCodeB'] == $to_code) {
        if ($type === 'cross' && isset($rate['rateCross'])) {
          return (float) $rate['rateCross'];
        }
        elseif ($type === 'buy' && isset($rate['rateBuy'])) {
          return (float) $rate['rateBuy'];
        }
        elseif ($type === 'sell' && isset($rate['rateSell'])) {
          return (float) $rate['rateSell'];
        }
      }
    }

    return NULL;
  }

  /**
   * Convert amount from one currency to another.
   *
   * @param float $amount
   *   Amount to convert.
   * @param int $from_code
   *   Source currency numeric code.
   * @param int $to_code
   *   Target currency numeric code.
   * @param string $type
   *   Rate type: 'buy' or 'sell'.
   *
   * @return float|null
   *   Converted amount or NULL if rate not found.
   */
  public function convert($amount, $from_code, $to_code, $type = 'buy') {
    // If same currency, return amount.
    if ($from_code == $to_code) {
      return $amount;
    }

    $rate = $this->getRate($from_code, $to_code, $type);

    // Try reverse rate.
    if ($rate === NULL) {
      $reverse_rate = $this->getRate($to_code, $from_code, $type === 'buy' ? 'sell' : 'buy');
      if ($reverse_rate !== NULL && $reverse_rate > 0) {
        $rate = 1 / $reverse_rate;
      }
    }

    // Try cross rate.
    if ($rate === NULL) {
      $rate = $this->getRate($from_code, $to_code, 'cross');
    }

    if ($rate !== NULL) {
      return $amount * $rate;
    }

    return NULL;
  }

  /**
   * Get all rates for a specific currency.
   *
   * @param int $currency_code
   *   Currency numeric code.
   *
   * @return array
   *   Array of rates involving this currency.
   */
  public function getRatesForCurrency($currency_code) {
    $rates = $this->getRates();
    $result = [];

    foreach ($rates as $rate) {
      if ($rate['currencyCodeA'] == $currency_code || $rate['currencyCodeB'] == $currency_code) {
        $result[] = $rate;
      }
    }

    return $result;
  }

  /**
   * Get formatted rates data for display.
   *
   * @return array
   *   Formatted rates data.
   */
  public function getFormattedRates() {
    $rates = $this->getRates();
    $formatted = [];

    foreach ($rates as $rate) {
      $formatted[] = [
        'currencyCodeA' => $rate['currencyCodeA'],
        'currencyCodeB' => $rate['currencyCodeB'],
        'date' => $rate['date'] ?? NULL,
        'rateBuy' => $rate['rateBuy'] ?? NULL,
        'rateSell' => $rate['rateSell'] ?? NULL,
        'rateCross' => $rate['rateCross'] ?? NULL,
      ];
    }

    return $formatted;
  }

  /**
   * Clear currency rates cache.
   */
  public function clearCache() {
    $this->cache->delete(self::CACHE_ID);
    $this->logger->info('Currency rates cache cleared.');
  }

}
