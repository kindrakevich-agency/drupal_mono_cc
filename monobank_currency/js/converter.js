/**
 * @file
 * Monobank Currency Converter JavaScript - Pure Vanilla JS.
 */

(function (drupalSettings) {
  'use strict';

  // Wait for DOM to be ready.
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    const rates = drupalSettings?.monobankCurrency?.rates || [];
    const currencies = drupalSettings?.monobankCurrency?.currencies || {};

    // Get elements.
    const swapButton = document.getElementById('swap-currencies');
    const amountInput = document.querySelector('input[name="amount"]');
    const fromSelect = document.querySelector('select[name="from_currency"]');
    const toSelect = document.querySelector('select[name="to_currency"]');
    const convertedAmount = document.getElementById('converted-amount');
    const currencySearch = document.getElementById('currency-search');

    if (!amountInput || !fromSelect || !toSelect || !convertedAmount) {
      return;
    }

    // Swap button functionality.
    if (swapButton) {
      swapButton.addEventListener('click', function(e) {
        e.preventDefault();
        const fromVal = fromSelect.value;
        const toVal = toSelect.value;
        fromSelect.value = toVal;
        toSelect.value = fromVal;
        calculateConversion();
        highlightCurrencyRows();
      });
    }

    // Live calculation on amount input.
    amountInput.addEventListener('input', calculateConversion);

    // Live calculation on currency change.
    fromSelect.addEventListener('change', function() {
      calculateConversion();
      highlightCurrencyRows();
    });

    toSelect.addEventListener('change', function() {
      calculateConversion();
      highlightCurrencyRows();
    });

    // Currency search functionality.
    if (currencySearch) {
      currencySearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#rates-table-body tr');

        rows.forEach(function(row) {
          const text = row.textContent.toLowerCase();
          if (text.indexOf(searchTerm) !== -1) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }

    // Initial calculation.
    calculateConversion();
    highlightCurrencyRows();

    /**
     * Calculate and display conversion.
     */
    function calculateConversion() {
      const amount = parseFloat(amountInput.value) || 0;
      const fromCode = parseInt(fromSelect.value);
      const toCode = parseInt(toSelect.value);

      if (amount && fromCode && toCode) {
        const rate = getRate(fromCode, toCode);
        if (rate !== null) {
          const converted = amount * rate;
          convertedAmount.textContent = converted.toFixed(2);
        } else {
          convertedAmount.textContent = '—';
        }
      } else {
        convertedAmount.textContent = '0.00';
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
      for (let i = 0; i < rates.length; i++) {
        const rate = rates[i];
        if (rate.currencyCodeA == fromCode && rate.currencyCodeB == toCode) {
          return rate.rateSell || rate.rateBuy || rate.rateCross || null;
        }
      }

      // Try reverse rate.
      for (let i = 0; i < rates.length; i++) {
        const rate = rates[i];
        if (rate.currencyCodeA == toCode && rate.currencyCodeB == fromCode) {
          const reverseRate = rate.rateBuy || rate.rateSell || rate.rateCross;
          if (reverseRate && reverseRate > 0) {
            return 1 / reverseRate;
          }
        }
      }

      // Try cross rate via UAH (980).
      let fromToUah = null;
      let toToUah = null;

      for (let i = 0; i < rates.length; i++) {
        const rate = rates[i];
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
      const fromCurrency = fromSelect.value;
      const toCurrency = toSelect.value;
      const rows = document.querySelectorAll('.rate-row');

      // Remove all highlights.
      rows.forEach(function(row) {
        row.classList.remove('bg-blue-50', 'border-l-4', 'border-l-blue-500');
      });

      // Highlight selected currencies.
      if (fromCurrency && fromCurrency != 980) {
        const fromRow = document.querySelector('.rate-row[data-currency-code="' + fromCurrency + '"]');
        if (fromRow) {
          fromRow.classList.add('bg-blue-50', 'border-l-4', 'border-l-blue-500');
        }
      }

      if (toCurrency && toCurrency != 980) {
        const toRow = document.querySelector('.rate-row[data-currency-code="' + toCurrency + '"]');
        if (toRow) {
          toRow.classList.add('bg-blue-50', 'border-l-4', 'border-l-blue-500');
        }
      }
    }
  }

})(drupalSettings);
