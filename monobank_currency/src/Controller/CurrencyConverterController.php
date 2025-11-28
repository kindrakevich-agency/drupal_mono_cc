<?php

namespace Drupal\monobank_currency\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\monobank_currency\Service\MonobankCurrencyService;
use Drupal\monobank_currency\Service\CurrencyMapperService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for currency converter page.
 */
class CurrencyConverterController extends ControllerBase {

  /**
   * The currency service.
   *
   * @var \Drupal\monobank_currency\Service\MonobankCurrencyService
   */
  protected $currencyService;

  /**
   * The currency mapper service.
   *
   * @var \Drupal\monobank_currency\Service\CurrencyMapperService
   */
  protected $currencyMapper;

  /**
   * Constructs a CurrencyConverterController object.
   *
   * @param \Drupal\monobank_currency\Service\MonobankCurrencyService $currency_service
   *   The currency service.
   * @param \Drupal\monobank_currency\Service\CurrencyMapperService $currency_mapper
   *   The currency mapper service.
   */
  public function __construct(MonobankCurrencyService $currency_service, CurrencyMapperService $currency_mapper) {
    $this->currencyService = $currency_service;
    $this->currencyMapper = $currency_mapper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('monobank_currency.currency_service'),
      $container->get('monobank_currency.currency_mapper')
    );
  }

  /**
   * Builds the currency converter page.
   *
   * @return array
   *   Render array.
   */
  public function build() {
    // Get converter form.
    $converter_form = $this->formBuilder()->getForm('Drupal\monobank_currency\Form\CurrencyConverterForm');

    // Get rates data.
    $rates = $this->currencyService->getFormattedRates();

    // Build rates table data.
    $table_data = $this->buildRatesTable($rates);

    // Get all currencies for JavaScript.
    $currencies = $this->currencyMapper->getAllCurrencies();

    $build = [
      '#theme' => 'monobank_currency_converter',
      '#converter_form' => $converter_form,
      '#rates_table' => $table_data,
      '#currencies' => $currencies,
      '#last_update' => $this->getLastUpdateTime(),
      '#attached' => [
        'library' => [
          'monobank_currency/converter',
        ],
        'drupalSettings' => [
          'monobankCurrency' => [
            'rates' => $rates,
            'currencies' => $currencies,
          ],
        ],
      ],
    ];

    // Check if Tailwind CDN should be enabled.
    $config = $this->config('monobank_currency.settings');
    if ($config->get('use_tailwind_cdn')) {
      $build['#attached']['html_head'][] = [
        [
          '#tag' => 'script',
          '#attributes' => [
            'src' => 'https://cdn.tailwindcss.com',
          ],
        ],
        'monobank_currency_tailwind_cdn',
      ];
    }

    return $build;
  }

  /**
   * Build rates table data.
   *
   * @param array $rates
   *   Raw rates data.
   *
   * @return array
   *   Formatted table data.
   */
  protected function buildRatesTable(array $rates) {
    $rows = [];

    // Get all currencies from mapper.
    $all_currencies = $this->currencyMapper->getAllCurrencies();

    // Build a map of currency codes to their rates.
    $rate_map = [];
    foreach ($rates as $rate) {
      $key = $rate['currencyCodeA'] . '_' . $rate['currencyCodeB'];
      $rate_map[$key] = $rate;
    }

    // Add a row for each currency.
    foreach ($all_currencies as $code => $currency_info) {
      // Get currency details.
      $currency_code = $currency_info['code'] ?? 'CUR' . $code;
      $currency_name = $currency_info['name'] ?? 'Currency ' . $code;
      $country = $currency_info['country'] ?? 'xx';
      $flag_url = $this->currencyMapper->getFlagUrl($country);

      // Initial rates are calculated to UAH (JavaScript will recalculate).
      $buy = '-';
      $sell = '-';
      $cross = '-';

      // Look for this currency's rate to UAH.
      $key = $code . '_980';
      if (isset($rate_map[$key])) {
        $rate = $rate_map[$key];
        if (isset($rate['rateBuy']) && isset($rate['rateSell'])) {
          $buy = number_format($rate['rateBuy'], 4);
          $sell = number_format($rate['rateSell'], 4);
          $cross = isset($rate['rateCross']) ? number_format($rate['rateCross'], 4) : '-';
        }
        elseif (isset($rate['rateCross'])) {
          $buy = number_format($rate['rateCross'], 4);
          $sell = number_format($rate['rateCross'], 4);
          $cross = number_format($rate['rateCross'], 4);
        }
      }

      $rows[] = [
        'code' => $code,
        'currency_code' => $currency_code,
        'currency_name' => $currency_name,
        'flag' => $flag_url,
        'buy' => $buy,
        'sell' => $sell,
        'cross' => $cross,
      ];
    }

    // Sort by currency code.
    usort($rows, function ($a, $b) {
      return strcmp($a['currency_code'], $b['currency_code']);
    });

    return $rows;
  }

  /**
   * Get last update time.
   *
   * @return string|null
   *   Formatted time or NULL.
   */
  protected function getLastUpdateTime() {
    $cache = \Drupal::cache()->get(MonobankCurrencyService::CACHE_ID);
    if ($cache && isset($cache->created)) {
      return \Drupal::service('date.formatter')->format($cache->created, 'custom', 'H:i');
    }
    return NULL;
  }

}
