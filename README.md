# Telegram Data Validator

The Telegram Data Validator is a PHP library for validating the integrity of initData received from a Telegram
mini-application. It uses HMAC-SHA-256 for data integrity checks.

## Installation

Install the library using Composer:

```shell
composer require vlsv/telegram-data-validator
```

## Documentation

```php
/**
 * Validates the integrity of the provided Telegram WebApp initData string received from a Telegram
 * mini-application.
 *
 * @param string $initData The initData string containing query parameters.
 * @param string $botToken The bot token used for HMAC calculation.
 * @param bool   $verbose  Whether to include additional information in the result.
 *
 * @return bool|array If $verbose is true, returns an associative array with validation information,
 *                    otherwise returns a boolean indicating whether the validation passed.
 */
InitData::isValid(string $initData, string $botToken, bool $verbose = false): bool|array
```

## Usage

```php
<?php

use Vlsv\TelegramInitDataValidator\Validator\InitData;

// Your bot token
$botToken = "<your-bot-token>";

// Your initData string
$initData = "query_id=AAGk...";

// Validate initData
$result = \Vlsv\TelegramInitDataValidator\Validator\InitData::isValid($initData, $botToken, true);

// Display the validation result
var_dump($result);
```

## Tests

```shell
composer tests
```

## License

This project is licensed under the [GNU General Public License v3](https://www.gnu.org/licenses/gpl-3.0.en.html).
