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

    // Focus on UAH pairs (980).
    foreach ($rates as $rate) {
      if ($rate['currencyCodeB'] == 980 && $rate['currencyCodeA'] != 980) {
        $currency_info = $this->currencyMapper->getCurrencyByCode($rate['currencyCodeA']);

        if ($currency_info) {
          $flag_url = $this->currencyMapper->getFlagUrl($currency_info['country']);

          $rows[] = [
            'code' => $rate['currencyCodeA'],
            'currency_code' => $currency_info['code'],
            'currency_name' => $currency_info['name'],
            'flag' => $flag_url,
            'buy' => isset($rate['rateBuy']) ? number_format($rate['rateBuy'], 4) : '-',
            'sell' => isset($rate['rateSell']) ? number_format($rate['rateSell'], 4) : '-',
            'cross' => isset($rate['rateCross']) ? number_format($rate['rateCross'], 4) : '-',
          ];
        }
      }
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
