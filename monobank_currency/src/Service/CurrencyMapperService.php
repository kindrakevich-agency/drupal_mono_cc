<?php

namespace Drupal\monobank_currency\Service;

/**
 * Service for mapping ISO 4217 currency codes to names and countries.
 */
class CurrencyMapperService {

  /**
   * Map of ISO 4217 numeric codes to currency information.
   *
   * @var array
   */
  protected $currencyMap = [
    840 => ['code' => 'USD', 'name' => 'US Dollar', 'country' => 'us'],
    978 => ['code' => 'EUR', 'name' => 'Euro', 'country' => 'eu'],
    980 => ['code' => 'UAH', 'name' => 'Ukrainian Hryvnia', 'country' => 'ua'],
    826 => ['code' => 'GBP', 'name' => 'British Pound', 'country' => 'gb'],
    392 => ['code' => 'JPY', 'name' => 'Japanese Yen', 'country' => 'jp'],
    756 => ['code' => 'CHF', 'name' => 'Swiss Franc', 'country' => 'ch'],
    156 => ['code' => 'CNY', 'name' => 'Chinese Yuan', 'country' => 'cn'],
    784 => ['code' => 'AED', 'name' => 'UAE Dirham', 'country' => 'ae'],
    971 => ['code' => 'AFN', 'name' => 'Afghan Afghani', 'country' => 'af'],
    8 => ['code' => 'ALL', 'name' => 'Albanian Lek', 'country' => 'al'],
    51 => ['code' => 'AMD', 'name' => 'Armenian Dram', 'country' => 'am'],
    973 => ['code' => 'AOA', 'name' => 'Angolan Kwanza', 'country' => 'ao'],
    32 => ['code' => 'ARS', 'name' => 'Argentine Peso', 'country' => 'ar'],
    36 => ['code' => 'AUD', 'name' => 'Australian Dollar', 'country' => 'au'],
    944 => ['code' => 'AZN', 'name' => 'Azerbaijani Manat', 'country' => 'az'],
    50 => ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'country' => 'bd'],
    975 => ['code' => 'BGN', 'name' => 'Bulgarian Lev', 'country' => 'bg'],
    48 => ['code' => 'BHD', 'name' => 'Bahraini Dinar', 'country' => 'bh'],
    108 => ['code' => 'BIF', 'name' => 'Burundian Franc', 'country' => 'bi'],
    96 => ['code' => 'BND', 'name' => 'Brunei Dollar', 'country' => 'bn'],
    68 => ['code' => 'BOB', 'name' => 'Bolivian Boliviano', 'country' => 'bo'],
    986 => ['code' => 'BRL', 'name' => 'Brazilian Real', 'country' => 'br'],
    72 => ['code' => 'BWP', 'name' => 'Botswana Pula', 'country' => 'bw'],
    933 => ['code' => 'BYN', 'name' => 'Belarusian Ruble', 'country' => 'by'],
    124 => ['code' => 'CAD', 'name' => 'Canadian Dollar', 'country' => 'ca'],
    976 => ['code' => 'CDF', 'name' => 'Congolese Franc', 'country' => 'cd'],
    152 => ['code' => 'CLP', 'name' => 'Chilean Peso', 'country' => 'cl'],
    170 => ['code' => 'COP', 'name' => 'Colombian Peso', 'country' => 'co'],
    188 => ['code' => 'CRC', 'name' => 'Costa Rican Colón', 'country' => 'cr'],
    192 => ['code' => 'CUP', 'name' => 'Cuban Peso', 'country' => 'cu'],
    203 => ['code' => 'CZK', 'name' => 'Czech Koruna', 'country' => 'cz'],
    262 => ['code' => 'DJF', 'name' => 'Djiboutian Franc', 'country' => 'dj'],
    208 => ['code' => 'DKK', 'name' => 'Danish Krone', 'country' => 'dk'],
    12 => ['code' => 'DZD', 'name' => 'Algerian Dinar', 'country' => 'dz'],
    818 => ['code' => 'EGP', 'name' => 'Egyptian Pound', 'country' => 'eg'],
    230 => ['code' => 'ETB', 'name' => 'Ethiopian Birr', 'country' => 'et'],
    981 => ['code' => 'GEL', 'name' => 'Georgian Lari', 'country' => 'ge'],
    936 => ['code' => 'GHS', 'name' => 'Ghanaian Cedi', 'country' => 'gh'],
    270 => ['code' => 'GMD', 'name' => 'Gambian Dalasi', 'country' => 'gm'],
    324 => ['code' => 'GNF', 'name' => 'Guinean Franc', 'country' => 'gn'],
    344 => ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'country' => 'hk'],
    191 => ['code' => 'HRK', 'name' => 'Croatian Kuna', 'country' => 'hr'],
    348 => ['code' => 'HUF', 'name' => 'Hungarian Forint', 'country' => 'hu'],
    360 => ['code' => 'IDR', 'name' => 'Indonesian Rupiah', 'country' => 'id'],
    376 => ['code' => 'ILS', 'name' => 'Israeli Shekel', 'country' => 'il'],
    356 => ['code' => 'INR', 'name' => 'Indian Rupee', 'country' => 'in'],
    368 => ['code' => 'IQD', 'name' => 'Iraqi Dinar', 'country' => 'iq'],
    352 => ['code' => 'ISK', 'name' => 'Icelandic Króna', 'country' => 'is'],
    400 => ['code' => 'JOD', 'name' => 'Jordanian Dinar', 'country' => 'jo'],
    404 => ['code' => 'KES', 'name' => 'Kenyan Shilling', 'country' => 'ke'],
    417 => ['code' => 'KGS', 'name' => 'Kyrgyzstani Som', 'country' => 'kg'],
    116 => ['code' => 'KHR', 'name' => 'Cambodian Riel', 'country' => 'kh'],
    410 => ['code' => 'KRW', 'name' => 'South Korean Won', 'country' => 'kr'],
    414 => ['code' => 'KWD', 'name' => 'Kuwaiti Dinar', 'country' => 'kw'],
    398 => ['code' => 'KZT', 'name' => 'Kazakhstani Tenge', 'country' => 'kz'],
    418 => ['code' => 'LAK', 'name' => 'Lao Kip', 'country' => 'la'],
    422 => ['code' => 'LBP', 'name' => 'Lebanese Pound', 'country' => 'lb'],
    144 => ['code' => 'LKR', 'name' => 'Sri Lankan Rupee', 'country' => 'lk'],
    434 => ['code' => 'LYD', 'name' => 'Libyan Dinar', 'country' => 'ly'],
    504 => ['code' => 'MAD', 'name' => 'Moroccan Dirham', 'country' => 'ma'],
    498 => ['code' => 'MDL', 'name' => 'Moldovan Leu', 'country' => 'md'],
    969 => ['code' => 'MGA', 'name' => 'Malagasy Ariary', 'country' => 'mg'],
    807 => ['code' => 'MKD', 'name' => 'Macedonian Denar', 'country' => 'mk'],
    496 => ['code' => 'MNT', 'name' => 'Mongolian Tögrög', 'country' => 'mn'],
    480 => ['code' => 'MUR', 'name' => 'Mauritian Rupee', 'country' => 'mu'],
    454 => ['code' => 'MWK', 'name' => 'Malawian Kwacha', 'country' => 'mw'],
    484 => ['code' => 'MXN', 'name' => 'Mexican Peso', 'country' => 'mx'],
    458 => ['code' => 'MYR', 'name' => 'Malaysian Ringgit', 'country' => 'my'],
    943 => ['code' => 'MZN', 'name' => 'Mozambican Metical', 'country' => 'mz'],
    516 => ['code' => 'NAD', 'name' => 'Namibian Dollar', 'country' => 'na'],
    566 => ['code' => 'NGN', 'name' => 'Nigerian Naira', 'country' => 'ng'],
    558 => ['code' => 'NIO', 'name' => 'Nicaraguan Córdoba', 'country' => 'ni'],
    578 => ['code' => 'NOK', 'name' => 'Norwegian Krone', 'country' => 'no'],
    524 => ['code' => 'NPR', 'name' => 'Nepalese Rupee', 'country' => 'np'],
    554 => ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'country' => 'nz'],
    512 => ['code' => 'OMR', 'name' => 'Omani Rial', 'country' => 'om'],
    604 => ['code' => 'PEN', 'name' => 'Peruvian Sol', 'country' => 'pe'],
    608 => ['code' => 'PHP', 'name' => 'Philippine Peso', 'country' => 'ph'],
    586 => ['code' => 'PKR', 'name' => 'Pakistani Rupee', 'country' => 'pk'],
    985 => ['code' => 'PLN', 'name' => 'Polish Złoty', 'country' => 'pl'],
    600 => ['code' => 'PYG', 'name' => 'Paraguayan Guaraní', 'country' => 'py'],
    634 => ['code' => 'QAR', 'name' => 'Qatari Riyal', 'country' => 'qa'],
    946 => ['code' => 'RON', 'name' => 'Romanian Leu', 'country' => 'ro'],
    941 => ['code' => 'RSD', 'name' => 'Serbian Dinar', 'country' => 'rs'],
    643 => ['code' => 'RUB', 'name' => 'Russian Ruble', 'country' => 'ru'],
    682 => ['code' => 'SAR', 'name' => 'Saudi Riyal', 'country' => 'sa'],
    690 => ['code' => 'SCR', 'name' => 'Seychellois Rupee', 'country' => 'sc'],
    938 => ['code' => 'SDG', 'name' => 'Sudanese Pound', 'country' => 'sd'],
    752 => ['code' => 'SEK', 'name' => 'Swedish Krona', 'country' => 'se'],
    702 => ['code' => 'SGD', 'name' => 'Singapore Dollar', 'country' => 'sg'],
    694 => ['code' => 'SLL', 'name' => 'Sierra Leonean Leone', 'country' => 'sl'],
    706 => ['code' => 'SOS', 'name' => 'Somali Shilling', 'country' => 'so'],
    968 => ['code' => 'SRD', 'name' => 'Surinamese Dollar', 'country' => 'sr'],
    748 => ['code' => 'SZL', 'name' => 'Swazi Lilangeni', 'country' => 'sz'],
    764 => ['code' => 'THB', 'name' => 'Thai Baht', 'country' => 'th'],
    972 => ['code' => 'TJS', 'name' => 'Tajikistani Somoni', 'country' => 'tj'],
    788 => ['code' => 'TND', 'name' => 'Tunisian Dinar', 'country' => 'tn'],
    949 => ['code' => 'TRY', 'name' => 'Turkish Lira', 'country' => 'tr'],
    901 => ['code' => 'TWD', 'name' => 'New Taiwan Dollar', 'country' => 'tw'],
    834 => ['code' => 'TZS', 'name' => 'Tanzanian Shilling', 'country' => 'tz'],
    800 => ['code' => 'UGX', 'name' => 'Ugandan Shilling', 'country' => 'ug'],
    858 => ['code' => 'UYU', 'name' => 'Uruguayan Peso', 'country' => 'uy'],
    860 => ['code' => 'UZS', 'name' => 'Uzbekistani Som', 'country' => 'uz'],
    704 => ['code' => 'VND', 'name' => 'Vietnamese Dong', 'country' => 'vn'],
    950 => ['code' => 'XAF', 'name' => 'Central African CFA Franc', 'country' => 'cf'],
    952 => ['code' => 'XOF', 'name' => 'West African CFA Franc', 'country' => 'sn'],
    886 => ['code' => 'YER', 'name' => 'Yemeni Rial', 'country' => 'ye'],
    710 => ['code' => 'ZAR', 'name' => 'South African Rand', 'country' => 'za'],
  ];

  /**
   * Get currency information by numeric ISO code.
   *
   * @param int $code
   *   The ISO 4217 numeric currency code.
   *
   * @return array|null
   *   Currency information or NULL if not found.
   */
  public function getCurrencyByCode($code) {
    return $this->currencyMap[$code] ?? NULL;
  }

  /**
   * Get all currencies.
   *
   * @return array
   *   All currency information.
   */
  public function getAllCurrencies() {
    return $this->currencyMap;
  }

  /**
   * Get flag URL for a currency.
   *
   * @param string $countryCode
   *   Two-letter country code.
   *
   * @return string
   *   Flag image URL from flagcdn.com.
   */
  public function getFlagUrl($countryCode) {
    return "https://flagcdn.com/24x18/{$countryCode}.png";
  }

  /**
   * Get currency code by numeric code.
   *
   * @param int $code
   *   The ISO 4217 numeric currency code.
   *
   * @return string|null
   *   Currency code (e.g., 'USD') or NULL if not found.
   */
  public function getCurrencyCode($code) {
    $currency = $this->getCurrencyByCode($code);
    return $currency ? $currency['code'] : NULL;
  }

  /**
   * Get currency name by numeric code.
   *
   * @param int $code
   *   The ISO 4217 numeric currency code.
   *
   * @return string|null
   *   Currency name or NULL if not found.
   */
  public function getCurrencyName($code) {
    $currency = $this->getCurrencyByCode($code);
    return $currency ? $currency['name'] : NULL;
  }

}
