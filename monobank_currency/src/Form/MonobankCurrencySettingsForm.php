<?php

namespace Drupal\monobank_currency\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\monobank_currency\Service\MonobankCurrencyService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Settings form for Monobank Currency module.
 */
class MonobankCurrencySettingsForm extends ConfigFormBase {

  /**
   * The currency service.
   *
   * @var \Drupal\monobank_currency\Service\MonobankCurrencyService
   */
  protected $currencyService;

  /**
   * Constructs a MonobankCurrencySettingsForm object.
   *
   * @param \Drupal\monobank_currency\Service\MonobankCurrencyService $currency_service
   *   The currency service.
   */
  public function __construct(MonobankCurrencyService $currency_service) {
    $this->currencyService = $currency_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('monobank_currency.currency_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['monobank_currency.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'monobank_currency_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('monobank_currency.settings');

    $form['general'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('General Settings'),
    ];

    $form['general']['info'] = [
      '#markup' => '<p>' . $this->t('The Monobank Currency Converter module fetches currency rates from the Monobank API every time cron runs (recommended: every 30 minutes).') . '</p>',
    ];

    $form['cache'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Cache Management'),
    ];

    $cache = \Drupal::cache()->get(MonobankCurrencyService::CACHE_ID);
    if ($cache && isset($cache->created)) {
      $last_update = \Drupal::service('date.formatter')->format($cache->created, 'long');
      $form['cache']['last_update'] = [
        '#markup' => '<p><strong>' . $this->t('Last Update:') . '</strong> ' . $last_update . '</p>',
      ];

      $rates_count = count($cache->data);
      $form['cache']['rates_count'] = [
        '#markup' => '<p><strong>' . $this->t('Cached Rates:') . '</strong> ' . $rates_count . '</p>',
      ];
    }
    else {
      $form['cache']['no_cache'] = [
        '#markup' => '<p>' . $this->t('No cached currency rates found. Run cron to fetch rates.') . '</p>',
      ];
    }

    $form['cache']['clear_cache'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clear Cache & Fetch Now'),
      '#submit' => ['::clearCacheSubmit'],
    ];

    $form['advanced'] = [
      '#type' => 'details',
      '#title' => $this->t('Advanced Settings'),
      '#open' => FALSE,
    ];

    $form['advanced']['api_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Monobank API URL'),
      '#default_value' => MonobankCurrencyService::API_URL,
      '#disabled' => TRUE,
      '#description' => $this->t('The API endpoint used to fetch currency rates. This is read-only.'),
    ];

    $form['advanced']['cache_duration'] = [
      '#markup' => '<p><strong>' . $this->t('Cache Duration:') . '</strong> ' . $this->t('30 minutes (1800 seconds)') . '</p>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Submit handler for clear cache button.
   */
  public function clearCacheSubmit(array &$form, FormStateInterface $form_state) {
    // Clear cache.
    $this->currencyService->clearCache();

    // Fetch new rates.
    $rates = $this->currencyService->getRates(TRUE);

    if (!empty($rates)) {
      $this->messenger()->addStatus($this->t('Cache cleared and @count currency rates fetched successfully.', [
        '@count' => count($rates),
      ]));
    }
    else {
      $this->messenger()->addWarning($this->t('Failed to fetch currency rates from Monobank API. Please check the logs.'));
    }

    // Rebuild the form to show updated cache info.
    $form_state->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
