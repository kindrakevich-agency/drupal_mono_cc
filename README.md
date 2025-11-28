# Monobank Currency Converter — Drupal 11 Module

## Overview

The Monobank Currency Converter module provides real-time currency exchange functionality in Drupal 11 using the official public Monobank API:
https://api.monobank.ua/bank/currency

The module fetches exchange rates from Monobank every 30 minutes via Drupal Cron, caches them locally, and exposes:
A currency converter page /currency
A Multilanguage
A dynamic currency rates table
A frontend calculator where selecting a currency updates the table and calculation results, example in converter.html
This module is intended for websites that need up-to-date exchange rates for Ukraine (UAH), USD, EUR, PLN, GBP, etc.

Features
✅ 1. Currency Converter
A user-friendly converter with two modes:

a) Convert From
Example:
Convert From:
USD – US Dollar
Amount to exchange:
100.00

b) Convert To
Example:
Convert To:
UAH – Ukrainian Hryvnia
Amount to receive:
3800.00

Currency rates are automatically applied using Monobank’s latest buy/sell values.

✅ 2. Dynamic Currency Table
A table that displays the current exchange rates (buy/sell).
When the user selects a currency in the converter (e.g., USD),
→ the table highlights/filters/updates to show relevant rows.
Supports all currencies returned by Monobank API.
Example columns:
Currency	Buy	Sell
USD	39.50	40.00
EUR	42.20	42.80
PLN	9.30	9.60

✅ 3. Monobank API Integration
Monobank endpoint:
https://api.monobank.ua/bank/currency

All currency after import must have flags (use any CDN flags lib). And proper names. We must save to our base also USD, US Dollar; UAH, Ukrainian Hryvnia. Make for this .po files: German, Ukrainian, Spanish.

[
  {
    "currencyCodeA": 840,
    "currencyCodeB": 980,
    "date": 1764280873,
    "rateBuy": 42.07,
    "rateSell": 42.5206
  },
  {
    "currencyCodeA": 978,
    "currencyCodeB": 980,
    "date": 1764280873,
    "rateBuy": 48.73,
    "rateSell": 49.4291
  },
  {
    "currencyCodeA": 978,
    "currencyCodeB": 840,
    "date": 1764280873,
    "rateBuy": 1.154,
    "rateSell": 1.164
  },
  {
    "currencyCodeA": 826,
    "currencyCodeB": 980,
    "date": 1764311823,
    "rateCross": 56.4353
  },
  {
    "currencyCodeA": 392,
    "currencyCodeB": 980,
    "date": 1764311837,
    "rateCross": 0.273
  },
  {
    "currencyCodeA": 756,
    "currencyCodeB": 980,
    "date": 1764311796,
    "rateCross": 53.0363
  },
  {
    "currencyCodeA": 156,
    "currencyCodeB": 980,
    "date": 1764311846,
    "rateCross": 6.0052
  },
  {
    "currencyCodeA": 784,
    "currencyCodeB": 980,
    "date": 1764311837,
    "rateCross": 11.5828
  },
  {
    "currencyCodeA": 971,
    "currencyCodeB": 980,
    "date": 1762952324,
    "rateCross": 0.6353
  },
  {
    "currencyCodeA": 8,
    "currencyCodeB": 980,
    "date": 1764311620,
    "rateCross": 0.5128
  },
  {
    "currencyCodeA": 51,
    "currencyCodeB": 980,
    "date": 1764311804,
    "rateCross": 0.1118
  },
  {
    "currencyCodeA": 973,
    "currencyCodeB": 980,
    "date": 1764181112,
    "rateCross": 0.0466
  },
  {
    "currencyCodeA": 32,
    "currencyCodeB": 980,
    "date": 1764308152,
    "rateCross": 0.0293
  },
  {
    "currencyCodeA": 36,
    "currencyCodeB": 980,
    "date": 1764311757,
    "rateCross": 27.8209
  },
  {
    "currencyCodeA": 944,
    "currencyCodeB": 980,
    "date": 1764310981,
    "rateCross": 25.0871
  },
  {
    "currencyCodeA": 50,
    "currencyCodeB": 980,
    "date": 1764310991,
    "rateCross": 0.3493
  },
  {
    "currencyCodeA": 975,
    "currencyCodeB": 980,
    "date": 1764311833,
    "rateCross": 25.2428
  },
  {
    "currencyCodeA": 48,
    "currencyCodeB": 980,
    "date": 1764281571,
    "rateCross": 112.764
  },
  {
    "currencyCodeA": 108,
    "currencyCodeB": 980,
    "date": 1715679129,
    "rateCross": 0.014
  },
  {
    "currencyCodeA": 96,
    "currencyCodeB": 980,
    "date": 1762681403,
    "rateCross": 32.4375
  },
  {
    "currencyCodeA": 68,
    "currencyCodeB": 980,
    "date": 1764242111,
    "rateCross": 6.2014
  },
  {
    "currencyCodeA": 986,
    "currencyCodeB": 980,
    "date": 1764303529,
    "rateCross": 7.9738
  },
  {
    "currencyCodeA": 72,
    "currencyCodeB": 980,
    "date": 1764267209,
    "rateCross": 3.4352
  },
  {
    "currencyCodeA": 933,
    "currencyCodeB": 980,
    "date": 1764280433,
    "rateCross": 14.5396
  },
  {
    "currencyCodeA": 124,
    "currencyCodeB": 980,
    "date": 1764311805,
    "rateCross": 30.3144
  },
  {
    "currencyCodeA": 976,
    "currencyCodeB": 980,
    "date": 1764246240,
    "rateCross": 0.0193
  },
  {
    "currencyCodeA": 152,
    "currencyCodeB": 980,
    "date": 1764298979,
    "rateCross": 0.0459
  },
  {
    "currencyCodeA": 170,
    "currencyCodeB": 980,
    "date": 1764309263,
    "rateCross": 0.0112
  },
  {
    "currencyCodeA": 188,
    "currencyCodeB": 980,
    "date": 1764305880,
    "rateCross": 0.0862
  },
  {
    "currencyCodeA": 192,
    "currencyCodeB": 980,
    "date": 1764194405,
    "rateCross": 1.7625
  },
  {
    "currencyCodeA": 203,
    "currencyCodeB": 980,
    "date": 1764311854,
    "rateCross": 2.0456
  },
  {
    "currencyCodeA": 262,
    "currencyCodeB": 980,
    "date": 1763376050,
    "rateCross": 0.2376
  },
  {
    "currencyCodeA": 208,
    "currencyCodeB": 980,
    "date": 1764311807,
    "rateCross": 6.6119
  },
  {
    "currencyCodeA": 12,
    "currencyCodeB": 980,
    "date": 1764308782,
    "rateCross": 0.3263
  },
  {
    "currencyCodeA": 818,
    "currencyCodeB": 980,
    "date": 1764311691,
    "rateCross": 0.8942
  },
  {
    "currencyCodeA": 230,
    "currencyCodeB": 980,
    "date": 1764233373,
    "rateCross": 0.2765
  },
  {
    "currencyCodeA": 981,
    "currencyCodeB": 980,
    "date": 1764311839,
    "rateCross": 15.9142
  },
  {
    "currencyCodeA": 936,
    "currencyCodeB": 980,
    "date": 1764268397,
    "rateCross": 3.8111
  },
  {
    "currencyCodeA": 270,
    "currencyCodeB": 980,
    "date": 1763312283,
    "rateCross": 0.5817
  },
  {
    "currencyCodeA": 324,
    "currencyCodeB": 980,
    "date": 1764271799,
    "rateCross": 0.0049
  },
  {
    "currencyCodeA": 344,
    "currencyCodeB": 980,
    "date": 1764311346,
    "rateCross": 5.4689
  },
  {
    "currencyCodeA": 191,
    "currencyCodeB": 980,
    "date": 1764194405,
    "rateCross": 6.5136
  },
  {
    "currencyCodeA": 348,
    "currencyCodeB": 980,
    "date": 1764311813,
    "rateCross": 0.1295
  },
  {
    "currencyCodeA": 360,
    "currencyCodeB": 980,
    "date": 1764311791,
    "rateCross": 0.0025
  },
  {
    "currencyCodeA": 376,
    "currencyCodeB": 980,
    "date": 1764311849,
    "rateCross": 13.0379
  },
  {
    "currencyCodeA": 356,
    "currencyCodeB": 980,
    "date": 1764311277,
    "rateCross": 0.4767
  },
  {
    "currencyCodeA": 368,
    "currencyCodeB": 980,
    "date": 1764310025,
    "rateCross": 0.0324
  },
  {
    "currencyCodeA": 352,
    "currencyCodeB": 980,
    "date": 1764303761,
    "rateCross": 0.3344
  },
  {
    "currencyCodeA": 400,
    "currencyCodeB": 980,
    "date": 1764309571,
    "rateCross": 60.06
  },
  {
    "currencyCodeA": 404,
    "currencyCodeB": 980,
    "date": 1764307770,
    "rateCross": 0.3277
  },
  {
    "currencyCodeA": 417,
    "currencyCodeB": 980,
    "date": 1764309891,
    "rateCross": 0.486
  },
  {
    "currencyCodeA": 116,
    "currencyCodeB": 980,
    "date": 1764309342,
    "rateCross": 0.0105
  },
  {
    "currencyCodeA": 410,
    "currencyCodeB": 980,
    "date": 1764311815,
    "rateCross": 0.0291
  },
  {
    "currencyCodeA": 414,
    "currencyCodeB": 980,
    "date": 1764301030,
    "rateCross": 138.6171
  },
  {
    "currencyCodeA": 398,
    "currencyCodeB": 980,
    "date": 1764311678,
    "rateCross": 0.0824
  },
  {
    "currencyCodeA": 418,
    "currencyCodeB": 980,
    "date": 1764311235,
    "rateCross": 0.0019
  },
  {
    "currencyCodeA": 422,
    "currencyCodeB": 980,
    "date": 1764275292,
    "rateCross": 0.0004
  },
  {
    "currencyCodeA": 144,
    "currencyCodeB": 980,
    "date": 1764310535,
    "rateCross": 0.138
  },
  {
    "currencyCodeA": 434,
    "currencyCodeB": 980,
    "date": 1762460905,
    "rateCross": 7.7337
  },
  {
    "currencyCodeA": 504,
    "currencyCodeB": 980,
    "date": 1764291481,
    "rateCross": 4.5456
  },
  {
    "currencyCodeA": 498,
    "currencyCodeB": 980,
    "date": 1764311855,
    "rateCross": 2.5237
  },
  {
    "currencyCodeA": 969,
    "currencyCodeB": 980,
    "date": 1763998993,
    "rateCross": 0.0094
  },
  {
    "currencyCodeA": 807,
    "currencyCodeB": 980,
    "date": 1764303074,
    "rateCross": 0.7979
  },
  {
    "currencyCodeA": 496,
    "currencyCodeB": 980,
    "date": 1762992470,
    "rateCross": 0.0118
  },
  {
    "currencyCodeA": 480,
    "currencyCodeB": 980,
    "date": 1764310023,
    "rateCross": 0.9238
  },
  {
    "currencyCodeA": 454,
    "currencyCodeB": 980,
    "date": 1763047816,
    "rateCross": 0.0246
  },
  {
    "currencyCodeA": 484,
    "currencyCodeB": 980,
    "date": 1764310773,
    "rateCross": 2.3203
  },
  {
    "currencyCodeA": 458,
    "currencyCodeB": 980,
    "date": 1764311857,
    "rateCross": 10.3138
  },
  {
    "currencyCodeA": 943,
    "currencyCodeB": 980,
    "date": 1764260799,
    "rateCross": 0.6718
  },
  {
    "currencyCodeA": 516,
    "currencyCodeB": 980,
    "date": 1764310715,
    "rateCross": 2.4868
  },
  {
    "currencyCodeA": 566,
    "currencyCodeB": 980,
    "date": 1764276300,
    "rateCross": 0.0294
  },
  {
    "currencyCodeA": 558,
    "currencyCodeB": 980,
    "date": 1764260657,
    "rateCross": 1.1608
  },
  {
    "currencyCodeA": 578,
    "currencyCodeB": 980,
    "date": 1764311852,
    "rateCross": 4.1932
  },
  {
    "currencyCodeA": 524,
    "currencyCodeB": 980,
    "date": 1764311348,
    "rateCross": 0.2973
  },
  {
    "currencyCodeA": 554,
    "currencyCodeB": 980,
    "date": 1764310500,
    "rateCross": 24.3793
  },
  {
    "currencyCodeA": 512,
    "currencyCodeB": 980,
    "date": 1764299535,
    "rateCross": 110.4467
  },
  {
    "currencyCodeA": 604,
    "currencyCodeB": 980,
    "date": 1764304490,
    "rateCross": 12.6489
  },
  {
    "currencyCodeA": 608,
    "currencyCodeB": 980,
    "date": 1764310875,
    "rateCross": 0.7242
  },
  {
    "currencyCodeA": 586,
    "currencyCodeB": 980,
    "date": 1764275059,
    "rateCross": 0.1517
  },
  {
    "currencyCodeA": 985,
    "currencyCodeB": 980,
    "date": 1764311858,
    "rateCross": 11.6585
  },
  {
    "currencyCodeA": 600,
    "currencyCodeB": 980,
    "date": 1764282689,
    "rateCross": 0.006
  },
  {
    "currencyCodeA": 634,
    "currencyCodeB": 980,
    "date": 1764311520,
    "rateCross": 11.6767
  },
  {
    "currencyCodeA": 946,
    "currencyCodeB": 980,
    "date": 1764311848,
    "rateCross": 9.7099
  },
  {
    "currencyCodeA": 941,
    "currencyCodeB": 980,
    "date": 1764311808,
    "rateCross": 0.4201
  },
  {
    "currencyCodeA": 682,
    "currencyCodeB": 980,
    "date": 1764310423,
    "rateCross": 11.3239
  },
  {
    "currencyCodeA": 690,
    "currencyCodeB": 980,
    "date": 1764310183,
    "rateCross": 2.8928
  },
  {
    "currencyCodeA": 938,
    "currencyCodeB": 980,
    "date": 1764194405,
    "rateCross": 0.0706
  },
  {
    "currencyCodeA": 752,
    "currencyCodeB": 980,
    "date": 1764311826,
    "rateCross": 4.4935
  },
  {
    "currencyCodeA": 702,
    "currencyCodeB": 980,
    "date": 1764311438,
    "rateCross": 32.8475
  },
  {
    "currencyCodeA": 694,
    "currencyCodeB": 980,
    "date": 1764194405,
    "rateCross": 0.0019
  },
  {
    "currencyCodeA": 706,
    "currencyCodeB": 980,
    "date": 1744215461,
    "rateCross": 0.073
  },
  {
    "currencyCodeA": 968,
    "currencyCodeB": 980,
    "date": 1719532361,
    "rateCross": 1.3118
  },
  {
    "currencyCodeA": 748,
    "currencyCodeB": 980,
    "date": 1749277852,
    "rateCross": 2.3545
  },
  {
    "currencyCodeA": 764,
    "currencyCodeB": 980,
    "date": 1764311861,
    "rateCross": 1.322
  },
  {
    "currencyCodeA": 972,
    "currencyCodeB": 980,
    "date": 1764225310,
    "rateCross": 4.5868
  },
  {
    "currencyCodeA": 788,
    "currencyCodeB": 980,
    "date": 1764266452,
    "rateCross": 14.4456
  },
  {
    "currencyCodeA": 949,
    "currencyCodeB": 980,
    "date": 1764311860,
    "rateCross": 1.0023
  },
  {
    "currencyCodeA": 901,
    "currencyCodeB": 980,
    "date": 1764311563,
    "rateCross": 1.358
  },
  {
    "currencyCodeA": 834,
    "currencyCodeB": 980,
    "date": 1764310686,
    "rateCross": 0.0174
  },
  {
    "currencyCodeA": 800,
    "currencyCodeB": 980,
    "date": 1764244981,
    "rateCross": 0.0117
  },
  {
    "currencyCodeA": 858,
    "currencyCodeB": 980,
    "date": 1764298902,
    "rateCross": 1.077
  },
  {
    "currencyCodeA": 860,
    "currencyCodeB": 980,
    "date": 1764310141,
    "rateCross": 0.0035
  },
  {
    "currencyCodeA": 704,
    "currencyCodeB": 980,
    "date": 1764311800,
    "rateCross": 0.0016
  },
  {
    "currencyCodeA": 950,
    "currencyCodeB": 980,
    "date": 1764271310,
    "rateCross": 0.0752
  },
  {
    "currencyCodeA": 952,
    "currencyCodeB": 980,
    "date": 1764177620,
    "rateCross": 0.0751
  },
  {
    "currencyCodeA": 886,
    "currencyCodeB": 980,
    "date": 1752053546,
    "rateCross": 0.168
  },
  {
    "currencyCodeA": 710,
    "currencyCodeB": 980,
    "date": 1764311778,
    "rateCross": 2.484
  }
]

Module supports:
API fetching service (MonobankCurrencyService)
JSON parsing
Mapping currency codes from ISO 4217
Handling API errors & fallback cache

✅ 4. Cron Caching (Every 30 Minutes)
The module retrieves and stores exchange rates in Drupal’s key-value cache.
Cron task runs every 30 minutes:
Calls Monobank API
Updates cached currency rates
Logs fetch result

Clears expired cache entries

If the API is unavailable, the module continues using the last valid cache.
