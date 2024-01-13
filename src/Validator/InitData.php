<?php

declare(strict_types=1);

namespace Vlsv\TelegramInitDataValidator\Validator;

/**
 * Class for validating the integrity of initData received from a Telegram mini-application.
 *
 * To validate data received via the Mini App, one should send the data from the Telegram.WebApp.initData field
 * to the bot's backend. The data is a query string, which is composed of a series of field-value pairs.
 *
 * @see https://core.telegram.org/bots/webapps#validating-data-received-via-the-mini-app
 */
class InitData
{
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
    public static function isValid(string $initData, string $botToken, bool $verbose = false): bool|array
    {
        if ($initData === '') {
            return $verbose ? ['isValid' => false, 'data' => []] : false;
        }

        $parsed = self::parseInitDataStringToArray($initData);
        $checkString = self::getCheckString($parsed);

        $algorithm = 'sha256';
        $secretKey = hash_hmac($algorithm, $botToken, 'WebAppData', true);
        $calculatedHash = bin2hex(hash_hmac($algorithm, $checkString, $secretKey, true));

        $result = [
            'isValid' => hash_equals($calculatedHash, $parsed['hash']),
            'data' => [
                'parsed' => $parsed,
                'calculatedHash' => $calculatedHash,
                'parsedHash' => $parsed['hash'],
                'checkString' => $checkString,
                'secretKey' => $secretKey,
            ],
        ];

        return $verbose ? $result : $result['isValid'];
    }

    /**
     * Parses the initData string into an associative array.
     *
     * @param string $initData The initData string containing query parameters.
     *
     * @return array The associative array parsed from the initData string.
     */
    private static function parseInitDataStringToArray(string $initData): array
    {
        parse_str($initData, $parsed);

        foreach ($parsed as $key => $value) {
            if (self::isValidJson($value)) {
                $parsed[$key] = json_decode(urldecode($value), true);
            }
        }

        return $parsed;
    }

    /**
     * Generates the check string for validation by sorting and formatting the provided data array.
     *
     * @param array $data The associative array containing the data for validation.
     *
     * @return string The formatted check string.
     */
    private static function getCheckString(array $data): string
    {
        unset($data['hash']);
        ksort($data);

        $array = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $array[] = $key . '=' . json_encode($value, JSON_UNESCAPED_UNICODE);
            } else {
                $array[] = $key . '=' . $value;
            }
        }

        return implode(PHP_EOL, $array);
    }

    /**
     * Checks if the given string is a valid JSON object.
     *
     * @param string $jsonString The string to check.
     *
     * @return bool Returns true if the string is a valid JSON object, otherwise false.
     */
    private static function isValidJson(string $jsonString): bool
    {
        json_decode($jsonString);

        return (json_last_error() === JSON_ERROR_NONE);
    }
}
