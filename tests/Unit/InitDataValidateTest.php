<?php

use PHPUnit\Framework\TestCase;
use Vlsv\TelegramInitDataValidator\Validator\InitData;

class InitDataValidateTest extends TestCase
{
    public static function validInitDataProvider(): array
    {
        $samplesDirectory = __DIR__ . '/../samples/initData/valid/';
        $files = glob($samplesDirectory . '*.txt');

        $data = [];

        foreach ($files as $file) {
            $key = pathinfo($file, PATHINFO_FILENAME);
            $data[$key] = [$key, trim(file_get_contents($file))];
        }

        return $data;
    }

    /**
     * @dataProvider validInitDataProvider
     */
    public function testIsValidVerbose(string $setName, string $initData)
    {
        $result = InitData::isValid($initData, getenv('TELEGRAM_BOT_TOKEN_SAMPLE'), true);

        self::assertArrayHasKey('data', $result);
        self::assertArrayHasKey('parsed', $result['data']);
        self::assertArrayHasKey('calculatedHash', $result['data']);
        self::assertArrayHasKey('parsedHash', $result['data']);
        self::assertArrayHasKey('checkString', $result['data']);
        self::assertArrayHasKey('secretKey', $result['data']);

        $parsed = $result['data']['parsed'];

        self::assertIsArray($parsed);
        self::assertArrayHasKey('user', $parsed);
        self::assertIsArray($parsed['user']);

        $user = $parsed['user'];

        self::assertEquals('78787878', $user['id']);
        self::assertEquals('キャラクターセット', $user['first_name']);
        self::assertEquals('last-name', $user['last_name']);
        self::assertEquals('characterset', $user['username']);
    }

    /**
     * @dataProvider validInitDataProvider
     */
    public function testIsValidValid(string $setName, string $initData)
    {
        $result = InitData::isValid($initData, getenv('TELEGRAM_BOT_TOKEN_SAMPLE'));

        self::assertTrue($result);
    }

    public static function invalidInitDataProvider(): array
    {
        $samplesDirectory = __DIR__ . '/../samples/initData/invalid/';
        $files = glob($samplesDirectory . '*.txt');

        $data = [];

        foreach ($files as $file) {
            $key = pathinfo($file, PATHINFO_FILENAME);
            $data[$key] = [$key, trim(file_get_contents($file))];
        }

        $data['empty'] = ['empty', ''];

        return $data;
    }

    /**
     * @dataProvider invalidInitDataProvider
     */
    public function testIsValidInvalid(string $setName, string $initData)
    {
        $result = InitData::isValid($initData, getenv('TELEGRAM_BOT_TOKEN'));

        self::assertFalse($result);
    }
}
