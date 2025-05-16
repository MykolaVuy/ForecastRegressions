# 📈 Forecast Regressions

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)
[![MIT License](https://img.shields.io/badge/license-MIT-darkgreen.svg)](LICENSE)
[![GitHub Repo](https://img.shields.io/badge/source-GitHub-black?logo=github)](https://github.com/MykolaVuy/ForecastRegressions)

A simple and extensible library for forecasting missing data points using different regression models: **Linear**, **Power**, and **Logarithmic**. Designed for PHP 8.1+.

---

## ✨ Features

- 📊 Supports **Linear**, **Power**, and **Logarithmic** regression.
- 🔍 Optional interpolation mode.
- ✅ Unit-tested and reliable.
- 🧩 Pluggable architecture via interface.

---

## 📂 Source Code

The complete source code is available on [GitHub](https://github.com/MykolaVuy/ForecastRegressions).

---

## 🛠 Installation

```bash
    composer require mykolavuy/forecast-regressions
```

---

## 🚀 Usage

```php
use MykolaVuy\Forecast\ForecastService;

$data = [
    1 => 10,
    2 => null,
    3 => 30,
    4 => null,
    5 => 50,
    6 => null,
];

$service = new ForecastService();

// Forecast using linear regression
$result = $service->forecast($data, method: 'linear');

// Power regression with interpolation only
$interpolated = $service->forecast($data, method: 'power', interpolateOnly: true);

```

---

## 🔧 Regression Methods

| Method        | Description                           |
|---------------|---------------------------------------|
| `linear`      | Straight-line fitting (y = ax + b)    |
| `power`       | Exponential growth/decay (y = ax^b)   |
| `logarithmic` | Log curve fitting (y = a + b\*log(x)) |

---

## ✅ Requirements

- PHP 8.1 or higher
- Composer

---

## 🧪 Running Tests

```bash
    ./vendor/bin/phpunit
``` 
>Test files are located in the tests/ directory.

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

## 🌐 Projects by the Author

### [intester.com](https://intester.com)

> **InTester** is a secure and transparent online knowledge assessment platform. It offers time-limited tests, anti-cheating measures, instant results with PDF certificates, and public test records — making it ideal for job seekers and recruiters alike.

### [dctsign.com](https://dctsign.com)

> **DCT Sign** is a blockchain-backed electronic signature platform that prioritizes privacy and data integrity. Users can securely sign documents without storing the original files, ensuring confidentiality and compliance with advanced e-signature standards.

---

*Thank you for using ForecastRegressions! Contributions and feedback are welcome.*
