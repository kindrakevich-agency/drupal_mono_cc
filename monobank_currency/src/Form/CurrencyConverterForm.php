<?php

namespace Drupal\monobank_currency\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\monobank_currency\Service\MonobankCurrencyService;
use Drupal\monobank_currency\Service\CurrencyMapperService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Currency converter form.
 */
class CurrencyConverterForm extends FormBase {

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
   * Constructs a CurrencyConverterForm object.
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
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'monobank_currency_converter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attributes']['class'][] = 'monobank-converter-form';

    // Get popular currencies for the dropdown.
    $currencies = $this->getPopularCurrencies();

    // Simple converter with from/to selects.
    $form['from_currency'] = [
      '#type' => 'select',
      '#options' => $currencies,
      '#default_value' => 840, // USD
    ];

    $form['to_currency'] = [
      '#type' => 'select',
      '#options' => $currencies,
      '#default_value' => 980, // UAH
    ];

    return $form;
  }

  /**
   * Get popular currencies for dropdown.
   *
   * @return array
   *   Array of currency options.
   */
  protected function getPopularCurrencies() {
    $popular = [840, 978, 980, 826, 985, 124, 36, 392, 156, 756, 578, 752];
    $options = [];

    foreach ($popular as $code) {
      $currency = $this->currencyMapper->getCurrencyByCode($code);
      if ($currency) {
        $options[$code] = $currency['code'] . ' – ' . $this->t($currency['name']);
      }
    }

    // Add all other currencies.
    $all_currencies = $this->currencyMapper->getAllCurrencies();
    foreach ($all_currencies as $code => $currency) {
      if (!isset($options[$code])) {
        $options[$code] = $currency['code'] . ' – ' . $this->t($currency['name']);
      }
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Form is AJAX-based, no submit action needed.
  }

}
