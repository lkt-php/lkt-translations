<?php


namespace Lkt\Translations\Loaders;


use Lkt\InstancePatterns\Traits\AutomaticInstanceTrait;
use Lkt\InstancePatterns\Traits\InstantiableTrait;
use Lkt\Translations\Helpers\TranslationHelper;
use function Couchbase\defaultDecoder;

/**
 * Class AllTranslationsLoader
 * @package Lkt\Translations\Loaders
 */
class AllTranslationsLoader
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

        return $r;
    }
}