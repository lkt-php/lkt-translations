<?php

namespace Lkt\Translations\Tests;

use Lkt\Translations\Translations;
use PHPUnit\Framework\TestCase;

class TranslationsTest extends TestCase
{
    /**
     * @return void
     */
    public function testStoreAndLoadTranslationsEngine()
    {
        // Load lang directories
        Translations::addLocalePath('en', __DIR__ . '/assets/en');
        Translations::addLocalePath('en_us', __DIR__ . '/assets/en_us');

        // Test available languages
        $this->assertEquals(['en', 'en_us'], Translations::getAvailableLanguages());

        // Test translations
        $this->assertEquals('Dolor sit amet', Translations::get('dolor.sit.amet'));
        $this->assertEquals('Dolor sit amet en_US', Translations::get('dolor.sit.amet', 'en_us'));

        // Test missed out translations between languages
        $missing = Translations::getMissedTranslations();
        $expectedMissing = [
            'en' => [
                'additionalKey' => ''
            ],
            'en_us' => [
                'additionalKey' => 'Hi'
            ]
        ];
        $this->assertEquals($expectedMissing, $missing);

        // Test export tool
        $exported = Translations::export();
        $expectedExported = [
            "en" => [
                "lorem" => "ipsum",
                "dolor.sit.amet" => "Dolor sit amet",
                "dolor.sit.amet1" => "Amet"
            ],
            "en_us" => [
                "lorem" => "ipsum",
                "dolor.sit.amet" => "Dolor sit amet en_US",
                "dolor.sit.amet1" => "Amet",
                "additionalKey" => "Hi"
            ]
        ];
        $this->assertEquals($expectedExported, $exported);

        // Test same value detected
        $sameValue = Translations::getTranslationsNotTranslated();
        $expectedSameValue = [
            "en" => [
                "lorem" => "ipsum",
                "dolor.sit.amet1" => "Amet",
            ],
            "en_us" => [
                "lorem" => "ipsum",
                "dolor.sit.amet1" => "Amet",
            ]
        ];
        $this->assertEquals($expectedSameValue, $sameValue);

    }
}