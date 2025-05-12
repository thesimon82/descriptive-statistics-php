# Descriptive Statistics for PHP

A lightweight, well‑tested PHP library that delivers the most common **descriptive‑statistics measures** in a single, strictly‑typed class.

|  |  |
|---|---|
| **Package** | `thesimon82/descriptive-statistics` |
| **License** | MIT |
| **PHP** | ≥ 8.1 |
| **Namespace** | `Renor\Statistics` |
| **Main class** | `DescriptiveStats` |

---

## 📦 Installation

```bash
composer require thesimon82/descriptive-statistics
```

No extra extensions are needed—pure PHP 8.1+.

---

## 🚀 Quick‑start

```php
<?php
declare(strict_types=1);

require 'vendor/autoload.php';

use Renor\Statistics\DescriptiveStats;

$data  = [2, 4, 4, 4, 5, 5, 7, 9];
$stats = new DescriptiveStats($data);

echo 'Mean (μ)             : ' . $stats->mean()                  . PHP_EOL; // 5
echo 'Median               : ' . $stats->median()                . PHP_EOL; // 4.5
echo 'Mode                 : ' . json_encode($stats->mode())     . PHP_EOL; // [4]
echo 'Var (population)     : ' . $stats->variance()              . PHP_EOL; // 4
echo 'St. dev (sample)     : ' . $stats->standardDeviation(true) . PHP_EOL; // 2.138…
echo 'IQR                  : ' . $stats->iqr()                   . PHP_EOL; // 2
echo 'MAD                  : ' . $stats->meanAbsoluteDeviation() . PHP_EOL; // 1.5
echo 'SEM                  : ' . $stats->standardError()         . PHP_EOL; // 0.756
echo '90th percentile      : ' . $stats->percentile(90)          . PHP_EOL; // 8.4
echo 'Min / Max            : ' . $stats->minValue() . ' / ' . $stats->maxValue() . PHP_EOL;
```

---

## 💡 API overview

### Central tendency

| Method | Description |
|--------|-------------|
| `mean()` | Arithmetic mean |
| `median()` | 50‑th percentile |
| `mode()` | Returns array – unimodal, multimodal or empty |
| `geometricMean()` | Requires all values **> 0** |
| `harmonicMean()`  | Requires all values **> 0** |
| `trimmedMean($p)` | Trims *p %* at each tail (0 ≤ p < 50) |

### Dispersion

| Method | Description |
|--------|-------------|
| `range()` | Max − min |
| `variance($sample = false)` | Population or sample |
| `standardDeviation($sample = false)` | σ or s |
| `meanAbsoluteDeviation()` | MAD |
| `quartiles()` | `[Q1, Q2, Q3]` (Tukey hinges) |
| `iqr()` | Q3 − Q1 |

### Uncertainty

| Method | Description |
|--------|-------------|
| `standardError()` | s / √n |

### Percentiles & extremes

| Method | Description |
|--------|-------------|
| `percentile($p)` | Linear interpolation, 0 ≤ p ≤ 100 |
| `minValue()` / `maxValue()` | Dataset bounds |

---

## 🧪 Running the tests

```bash
composer install          # if you haven't yet
vendor/bin/phpunit
```

The suite covers **100 %** of the public API, including edge cases (single value, invalid parameters).

---

## 🤝 Contributing

1. Fork & create a feature branch  
2. Follow **PSR‑12** and keep everything **strict_types=1**  
3. Add PHPUnit tests for any change  
4. Open a pull request – discussions and improvements are welcome!

---

## 📄 License

Released under the MIT License
