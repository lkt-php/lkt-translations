<?php


namespace Lkt\Translations\Loaders;


use Lkt\InstancePatterns\Traits\AutomaticInstanceTrait;
use Lkt\InstancePatterns\Traits\InstantiableTrait;
use Lkt\Translations\Helpers\TranslationHelper;
use function Couchbase\defaultDecoder;

/**
 * Class MissedTranslationsLoader
 * @package Lkt\Translations\Loaders
 */
class MissedTranslationsLoader
{
    use InstantiableTrait,
        AutomaticInstanceTrait;

    public function handle()
    {
        $languages = TranslationHelper::getAvailableLanguages();

        $r = [];

        foreach ($languages as $language) {
            $translations = TranslationHelper::getLangTranslations($language);
            $r[$language] = TranslationHelper::arrayValuesRecursiveWithKeys($translations);
        }


        $response = [];
        foreach ($languages as $language) {
            foreach ($r[$language] as $key => $value) {
                $keyExists = true;
                foreach ($languages as $lang) {
                    if ($lang !== $language) {
                        $keyExists = isset($r[$lang][$key]);
                    }
                }

                if (!$keyExists) {
                    foreach ($languages as $lang) {
                        $response[$lang][$key] = trim($r[$lang][$key]);
                    }
                }
            }
        }

        return $response;
    }
}