/**
 * @file
 * Monobank Currency Converter JavaScript - Pure Vanilla JS.
 */

(function (drupalSettings) {
  'use strict';

  // Currency code to country code mapping for flags.
  const currencyToCountry = {
    840: 'us',   // USD
    978: 'eu',   // EUR
    980: 'ua',   // UAH
    826: 'gb',   // GBP
    392: 'jp',   // JPY
    756: 'ch',   // CHF
    156: 'cn',   // CNY
    985: 'pl',   // PLN
    124: 'ca',   // CAD
    36: 'au',    // AUD
    578: 'no',   // NOK
    752: 'se',   // SEK
    208: 'dk',   // DKK
    203: 'cz',   // CZK
    348: 'hu',   // HUF
    946: 'ro',   // RON
    975: 'bg',   // BGN
    191: 'hr',   // HRK
  };

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
    const amountInput = document.getElementById('currency-amount');
    const fromSelect = document.getElementById('from-currency-select');
    const toSelect = document.getElementById('to-currency-select');
    const convertedAmount = document.getElementById('converted-amount');
    const currencySearch = document.getElementById('currency-search');
    const fromFlag = document.getElementById('from-flag');
    const toFlag = document.getElementById('to-flag');
    const tableHeader = document.getElementById('rates-table-header');
    const copyButton = document.getElementById('copy-button');

    if (!amountInput || !fromSelect || !toSelect || !convertedAmount) {
      return;
    }

    // Update flags and header initially.
    updateFlags();
    updateTableHeader();

    // Swap button functionality.
    if (swapButton) {
      swapButton.addEventListener('click', function(e) {
        e.preventDefault();
        const fromVal = fromSelect.value;
        const toVal = toSelect.value;
        fromSelect.value = toVal;
        toSelect.value = fromVal;
        updateFlags();
        calculateConversion();
        highlightCurrencyRows();
      });
    }

    // Copy button functionality.
    if (copyButton) {
      copyButton.addEventListener('click', function(e) {
        e.preventDefault();
        const value = convertedAmount.textContent;

        // Use Clipboard API (modern browsers including mobile).
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(value).then(function() {
            // Show success feedback.
            const originalText = copyButton.textContent;
            copyButton.textContent = 'Copied!';
            copyButton.classList.add('bg-green-100', 'text-green-700');
            copyButton.classList.remove('bg-gray-100', 'text-gray-700');

            setTimeout(function() {
              copyButton.textContent = originalText;
              copyButton.classList.remove('bg-green-100', 'text-green-700');
              copyButton.classList.add('bg-gray-100', 'text-gray-700');
            }, 2000);
          }).catch(function(err) {
            console.error('Failed to copy:', err);
          });
        } else {
          // Fallback for older browsers.
          const textArea = document.createElement('textarea');
          textArea.value = value;
          textArea.style.position = 'fixed';
          textArea.style.left = '-999999px';
          document.body.appendChild(textArea);
          textArea.select();
          try {
            document.execCommand('copy');
            // Show success feedback.
            const originalText = copyButton.textContent;
            copyButton.textContent = 'Copied!';
            copyButton.classList.add('bg-green-100', 'text-green-700');
            copyButton.classList.remove('bg-gray-100', 'text-gray-700');

            setTimeout(function() {
              copyButton.textContent = originalText;
              copyButton.classList.remove('bg-green-100', 'text-green-700');
              copyButton.classList.add('bg-gray-100', 'text-gray-700');
            }, 2000);
          } catch (err) {
            console.error('Fallback copy failed:', err);
          }
          document.body.removeChild(textArea);
        }
      });
    }

    // Live calculation on amount input.
    amountInput.addEventListener('input', function() {
      calculateConversion();
      updateTableRates();
    });

    // Live calculation on currency change.
    fromSelect.addEventListener('change', function() {
      updateFlags();
      updateTableHeader();
      calculateConversion();
      updateTableRates();
      highlightCurrencyRows();
    });

    toSelect.addEventListener('change', function() {
      updateFlags();
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
    updateTableRates();

    /**
     * Update flag images based on selected currencies.
     */
    function updateFlags() {
      const fromCode = parseInt(fromSelect.value);
      const toCode = parseInt(toSelect.value);

      const fromCountry = currencyToCountry[fromCode] || currencies[fromCode]?.country || 'us';
      const toCountry = currencyToCountry[toCode] || currencies[toCode]?.country || 'ua';

      if (fromFlag) {
        fromFlag.src = `https://flagcdn.com/24x18/${fromCountry}.png`;
        fromFlag.alt = fromCountry.toUpperCase();
      }

      if (toFlag) {
        toFlag.src = `https://flagcdn.com/24x18/${toCountry}.png`;
        toFlag.alt = toCountry.toUpperCase();
      }
    }

    /**
     * Update table header based on selected currency.
     */
    function updateTableHeader() {
      if (!tableHeader) return;

      const fromCode = parseInt(fromSelect.value);
      const currencyCode = currencies[fromCode]?.code || 'UAH';
      tableHeader.textContent = 'Exchange Rates (' + currencyCode + ')';
    }

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

    /**
     * Update table rates based on current amount and selected currency.
     */
    function updateTableRates() {
      const amount = parseFloat(amountInput.value) || 1;
      const fromCode = parseInt(fromSelect.value);
      const rows = document.querySelectorAll('.rate-row');

      rows.forEach(function(row) {
        const toCurrencyCode = parseInt(row.getAttribute('data-currency-code'));

        // Get the conversion rate from selected currency to this row's currency.
        const conversionRate = getRate(fromCode, toCurrencyCode);

        // Get the table cells.
        const cells = row.querySelectorAll('td');
        if (cells.length >= 4) {
          const buyCell = cells[1];
          const sellCell = cells[2];
          const crossCell = cells[3];

          if (conversionRate !== null) {
            const value = (conversionRate * amount).toFixed(4);
            buyCell.textContent = value;
            sellCell.textContent = value;
            crossCell.textContent = value;
          } else {
            buyCell.textContent = '-';
            sellCell.textContent = '-';
            crossCell.textContent = '-';
          }
        }
      });
    }
  }

})(drupalSettings);
