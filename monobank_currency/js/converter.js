/**
 * @file
 * Monobank Currency Converter JavaScript.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.monobankCurrencyConverter = {
    attach: function (context, settings) {
      var $converter = $('.monobank-currency-converter', context);

      if ($converter.length === 0) {
        return;
      }

      // Highlight rows when currency is selected.
      $('select[name="from_currency"], select[name="to_currency"]', $converter).once('currency-highlight').on('change', function() {
        highlightCurrencyRows();
      });

      // Initial highlight.
      highlightCurrencyRows();

      /**
       * Highlight table rows based on selected currencies.
       */
      function highlightCurrencyRows() {
        var fromCurrency = $('select[name="from_currency"]').val();
        var toCurrency = $('select[name="to_currency"]').val();

        // Remove all highlights.
        $('.rate-row', $converter).removeClass('highlighted');

        // Highlight selected currencies.
        if (fromCurrency && fromCurrency != 980) {
          $('.rate-row[data-currency-code="' + fromCurrency + '"]', $converter).addClass('highlighted');
        }
        if (toCurrency && toCurrency != 980) {
          $('.rate-row[data-currency-code="' + toCurrency + '"]', $converter).addClass('highlighted');
        }

        // Scroll to first highlighted row.
        var $firstHighlighted = $('.rate-row.highlighted', $converter).first();
        if ($firstHighlighted.length) {
          $firstHighlighted[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }

      // Add swap functionality if swap button exists.
      $('.swap-currencies', $converter).once('swap-handler').on('click', function(e) {
        e.preventDefault();

        var $fromSelect = $('select[name="from_currency"]', $converter);
        var $toSelect = $('select[name="to_currency"]', $converter);

        var fromVal = $fromSelect.val();
        var toVal = $toSelect.val();

        $fromSelect.val(toVal).trigger('change');
        $toSelect.val(fromVal).trigger('change');
      });

      // Real-time calculation for amount inputs.
      $('input[name="amount"], input[name="target_amount"]', $converter).once('amount-calculator').on('input', function() {
        // Trigger AJAX update via Drupal's AJAX framework.
        $(this).trigger('change');
      });

      // Add currency search/filter functionality.
      if ($('.rates-table', $converter).length) {
        addTableSearch();
      }

      /**
       * Add search functionality to rates table.
       */
      function addTableSearch() {
        var $table = $('.rates-table', $converter);
        var $tbody = $table.find('tbody');

        // Create search input if it doesn't exist.
        if ($('.table-search', $converter).length === 0) {
          var $searchWrapper = $('<div class="table-search-wrapper"></div>');
          var $searchInput = $('<input type="text" class="table-search" placeholder="' + Drupal.t('Search currencies...') + '">');

          $searchWrapper.append($searchInput);
          $table.before($searchWrapper);

          $searchInput.on('input', function() {
            var searchTerm = $(this).val().toLowerCase();

            $tbody.find('tr').each(function() {
              var $row = $(this);
              var currencyCode = $row.find('.currency-code').text().toLowerCase();
              var currencyName = $row.find('.currency-name').text().toLowerCase();

              if (currencyCode.indexOf(searchTerm) !== -1 || currencyName.indexOf(searchTerm) !== -1) {
                $row.show();
              } else {
                $row.hide();
              }
            });
          });
        }
      }

      // Format numbers in result display.
      $('.conversion-result', $converter).once('number-formatter').each(function() {
        var $result = $(this);
        // Numbers are already formatted server-side, this is for future enhancements.
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
