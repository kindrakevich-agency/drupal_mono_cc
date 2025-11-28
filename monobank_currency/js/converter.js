/**
 * @file
 * Monobank Currency Converter JavaScript.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.monobankCurrencyConverter = {
    attach: function (context, settings) {
      var rates = drupalSettings.monobankCurrency?.rates || [];
      var currencies = drupalSettings.monobankCurrency?.currencies || {};

      // Swap button functionality.
      $('#swap-currencies', context).once('swap-handler').on('click', function(e) {
        e.preventDefault();

        var $fromSelect = $('select[name="from_currency"]');
        var $toSelect = $('select[name="to_currency"]');

        var fromVal = $fromSelect.val();
        var toVal = $toSelect.val();

        $fromSelect.val(toVal);
        $toSelect.val(fromVal);

        // Recalculate.
        calculateConversion();
      });

      // Live calculation on amount input.
      $('input[name="amount"]', context).once('amount-calc').on('input', function() {
        calculateConversion();
      });

      // Live calculation on currency change.
      $('select[name="from_currency"], select[name="to_currency"]', context).once('currency-calc').on('change', function() {
        calculateConversion();
        highlightCurrencyRows();
      });

      // Initial calculation.
      calculateConversion();
      highlightCurrencyRows();

      /**
       * Calculate and display conversion.
       */
      function calculateConversion() {
        var amount = parseFloat($('input[name="amount"]').val()) || 0;
        var fromCode = parseInt($('select[name="from_currency"]').val());
        var toCode = parseInt($('select[name="to_currency"]').val());

        if (amount && fromCode && toCode) {
          var rate = getRate(fromCode, toCode);
          if (rate !== null) {
            var converted = amount * rate;
            $('#converted-amount').text(converted.toFixed(2));
          } else {
            $('#converted-amount').text('—');
          }
        }
      }

      /**
       * Get exchange rate between two currencies.
       */
      function getRate(fromCode, toCode) {
        if (fromCode === toCode) {
          return 1;
        }

        // Find direct rate.
        for (var i = 0; i < rates.length; i++) {
          var rate = rates[i];
          if (rate.currencyCodeA == fromCode && rate.currencyCodeB == toCode) {
            return rate.rateSell || rate.rateBuy || rate.rateCross || null;
          }
        }

        // Try reverse rate.
        for (var i = 0; i < rates.length; i++) {
          var rate = rates[i];
          if (rate.currencyCodeA == toCode && rate.currencyCodeB == fromCode) {
            var reverseRate = rate.rateBuy || rate.rateSell || rate.rateCross;
            if (reverseRate && reverseRate > 0) {
              return 1 / reverseRate;
            }
          }
        }

        // Try cross rate via UAH (980).
        var fromToUah = null;
        var toToUah = null;

        for (var i = 0; i < rates.length; i++) {
          var rate = rates[i];
          if (rate.currencyCodeA == fromCode && rate.currencyCodeB == 980) {
            fromToUah = rate.rateSell || rate.rateBuy || rate.rateCross;
          }
          if (rate.currencyCodeA == toCode && rate.currencyCodeB == 980) {
            toToUah = rate.rateSell || rate.rateBuy || rate.rateCross;
          }
        }

        if (fromToUah && toToUah) {
          return fromToUah / toToUah;
        }

        return null;
      }

      /**
       * Highlight table rows based on selected currencies.
       */
      function highlightCurrencyRows() {
        var fromCurrency = $('select[name="from_currency"]').val();
        var toCurrency = $('select[name="to_currency"]').val();

        // Remove all highlights.
        $('.rate-row').removeClass('bg-blue-50 border-l-4 border-l-blue-500');

        // Highlight selected currencies.
        if (fromCurrency && fromCurrency != 980) {
          $('.rate-row[data-currency-code="' + fromCurrency + '"]').addClass('bg-blue-50 border-l-4 border-l-blue-500');
        }
        if (toCurrency && toCurrency != 980) {
          $('.rate-row[data-currency-code="' + toCurrency + '"]').addClass('bg-blue-50 border-l-4 border-l-blue-500');
        }
      }

      // Currency search functionality.
      $('#currency-search', context).once('currency-search').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();

        $('#rates-table-body tr').each(function() {
          var $row = $(this);
          var text = $row.text().toLowerCase();

          if (text.indexOf(searchTerm) !== -1) {
            $row.show();
          } else {
            $row.hide();
          }
        });
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
