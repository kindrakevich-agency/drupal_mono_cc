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

    // Mode selector.
    $form['mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('Conversion Mode'),
      '#options' => [
        'from' => $this->t('Convert From'),
        'to' => $this->t('Convert To'),
      ],
      '#default_value' => $form_state->getValue('mode', 'from'),
      '#ajax' => [
        'callback' => '::ajaxUpdateForm',
        'wrapper' => 'converter-wrapper',
      ],
    ];

    $form['converter'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'converter-wrapper'],
    ];

    $mode = $form_state->getValue('mode', 'from');

    if ($mode === 'from') {
      // Convert From mode.
      $form['converter']['from_currency'] = [
        '#type' => 'select',
        '#title' => $this->t('Convert From'),
        '#options' => $currencies,
        '#default_value' => $form_state->getValue('from_currency', 840),
        '#ajax' => [
          'callback' => '::ajaxCalculate',
          'wrapper' => 'result-wrapper',
        ],
      ];

      $form['converter']['amount'] = [
        '#type' => 'number',
        '#title' => $this->t('Amount to exchange'),
        '#default_value' => $form_state->getValue('amount', 100),
        '#step' => 0.01,
        '#min' => 0,
        '#ajax' => [
          'callback' => '::ajaxCalculate',
          'wrapper' => 'result-wrapper',
        ],
      ];

      $form['converter']['to_currency'] = [
        '#type' => 'select',
        '#title' => $this->t('To'),
        '#options' => $currencies,
        '#default_value' => $form_state->getValue('to_currency', 980),
        '#ajax' => [
          'callback' => '::ajaxCalculate',
          'wrapper' => 'result-wrapper',
        ],
      ];
    }
    else {
      // Convert To mode.
      $form['converter']['to_currency'] = [
        '#type' => 'select',
        '#title' => $this->t('Convert To'),
        '#options' => $currencies,
        '#default_value' => $form_state->getValue('to_currency', 980),
        '#ajax' => [
          'callback' => '::ajaxCalculate',
          'wrapper' => 'result-wrapper',
        ],
      ];

      $form['converter']['target_amount'] = [
        '#type' => 'number',
        '#title' => $this->t('Amount to receive'),
        '#default_value' => $form_state->getValue('target_amount', 3800),
        '#step' => 0.01,
        '#min' => 0,
        '#ajax' => [
          'callback' => '::ajaxCalculate',
          'wrapper' => 'result-wrapper',
        ],
      ];

      $form['converter']['from_currency'] = [
        '#type' => 'select',
        '#title' => $this->t('From'),
        '#options' => $currencies,
        '#default_value' => $form_state->getValue('from_currency', 840),
        '#ajax' => [
          'callback' => '::ajaxCalculate',
          'wrapper' => 'result-wrapper',
        ],
      ];
    }

    $form['result'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'result-wrapper', 'class' => ['conversion-result']],
    ];

    // Calculate and display result.
    $result = $this->calculateConversion($form_state);
    if ($result !== NULL) {
      $from_currency_info = $this->currencyMapper->getCurrencyByCode($form_state->getValue('from_currency', 840));
      $to_currency_info = $this->currencyMapper->getCurrencyByCode($form_state->getValue('to_currency', 980));

      if ($mode === 'from') {
        $form['result']['output'] = [
          '#markup' => '<div class="result-display"><strong>' . $this->t('Result:') . '</strong> ' .
            number_format($result, 2) . ' ' . $to_currency_info['code'] . '</div>',
        ];
      }
      else {
        $form['result']['output'] = [
          '#markup' => '<div class="result-display"><strong>' . $this->t('Required:') . '</strong> ' .
            number_format($result, 2) . ' ' . $from_currency_info['code'] . '</div>',
        ];
      }
    }

    return $form;
  }

  /**
   * AJAX callback to update form.
   */
  public function ajaxUpdateForm(array &$form, FormStateInterface $form_state) {
    return $form['converter'];
  }

  /**
   * AJAX callback to calculate conversion.
   */
  public function ajaxCalculate(array &$form, FormStateInterface $form_state) {
    return $form['result'];
  }

  /**
   * Calculate currency conversion.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return float|null
   *   Converted amount or NULL.
   */
  protected function calculateConversion(FormStateInterface $form_state) {
    $mode = $form_state->getValue('mode', 'from');
    $from = $form_state->getValue('from_currency', 840);
    $to = $form_state->getValue('to_currency', 980);

    if ($mode === 'from') {
      $amount = $form_state->getValue('amount', 100);
      return $this->currencyService->convert($amount, $from, $to, 'sell');
    }
    else {
      $target_amount = $form_state->getValue('target_amount', 3800);
      // Calculate how much source currency is needed.
      $reverse = $this->currencyService->convert($target_amount, $to, $from, 'buy');
      return $reverse;
    }
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
