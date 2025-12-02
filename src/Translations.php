<?php

namespace Lkt\Translations;

use Lkt\Locale\Locale;
use Lkt\Translations\DTO\FeedWithReferenceDataResponse;
use function Lkt\Tools\Arrays\arrayValuesRecursiveWithKeys;
use function Lkt\Tools\Arrays\getArrayFirstPosition;
use function Lkt\Tools\Export\varToPHPCode;

class Translations
{
    protected static array $stack = [];
    protected static array $paths = [];
    protected static ?string $lang = null;

    protected static array $fallbackLanguages = [];
    protected static array $customFallbackLanguages = [];

    public static function setLang(string $lang): void
    {
        static::$lang = $lang;
    }

    public static function setFallbackLanguages(array $fallbackLanguages): void
    {
        static::$fallbackLanguages = array_unique($fallbackLanguages);
    }

    public static function setCustomFallbackLanguages(string $lang, array $fallbackLanguages): void
    {
        static::$customFallbackLanguages[$lang] = array_unique($fallbackLanguages);
    }

    public static function get(string $key, ?string $lang = null): mixed
    {
        $lang = static::determineLang($lang);
        $i18n = static::getLangTranslations($lang);

        $walk = explode('.', $key);
        $dig = $i18n;
        foreach ($walk as $step) {
            if (is_array($dig)) {
                $dig = $dig[$step];
            } else {
                break;
            }
        }

        if (is_null($dig)) {
            $fallbackResponse = null;

            if (is_array(static::$customFallbackLanguages[$lang]) && count(static::$customFallbackLanguages[$lang]) > 0) {
                foreach (static::$customFallbackLanguages[$lang] as $fallback) {
                    $aux = static::get($key, $fallback);
                    if (!is_null($aux) ) {
                        $fallbackResponse = $aux;
                        break;
                    }
                }
            }

            if (!is_null($fallbackResponse)) {
                return $fallbackResponse;
            }

            if (count(static::$fallbackLanguages) > 0) {
                foreach (static::$fallbackLanguages as $fallback) {
                    $aux = static::get($key, $fallback);
                    if (!is_null($aux) ) {
                        $fallbackResponse = $aux;
                        break;
                    }
                }
            }

            if (!is_null($fallbackResponse)) {
                return $fallbackResponse;
            }
        }

        return $dig;
    }

    public static function set($path, $value, ?string $lang = null): void
    {
        $lang = static::determineLang($lang);
        static::getLangTranslations($lang);
        $temp =& static::$stack[$lang];

        $path = explode('.', $path);
        foreach ($path as $key) {
            $temp = &$temp[$key];
        }
        $temp = $value;
    }

    public static function unset($path, ?string $lang = null): void
    {
        $lang = static::determineLang($lang);
        static::getLangTranslations($lang);
        $temp =& static::$stack[$lang];

        $path = explode('.', $path);

        foreach ($path as $key) {
            if (!is_array($temp[$key])) {
                unset($temp[$key]);
            } else {
                $temp =& $temp[$key];
            }
        }
    }

    public static function replaceTextRecursively(string|array $search, string|array $replacement, ?string $lang = null): void
    {
        $lang = static::determineLang($lang);
        static::getLangTranslations($lang);
        static::replaceText(static::$stack[$lang], $search, $replacement);
    }

    private static function replaceText(array &$stack, string|array $search, string|array $replacement): array
    {
        foreach ($stack as $key => &$value) {
            if (is_array($value)) {
                $stack[$key] = static::replaceText($value, $search, $replacement);
            } else {
                $stack[$key] = str_replace($search, $replacement, $value);
            }
        }
        return $stack;
    }

    public static function getLangTranslations(string $lang = ''): array
    {
        if (!$lang) $lang = Locale::getLangCode();
        if (!isset(static::$stack[$lang]) || !is_array(static::$stack[$lang])) {

            $r = [];
            foreach (static::$paths[$lang] as $path) {
                $files = scandir($path);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..' || is_dir("{$path}/{$file}")) {
                        continue;
                    }

                    $data = require "{$path}/{$file}";
                    $r = array_merge($r, $data);
                }
            }

            static::$stack[$lang] = $r;
        }

        return static::$stack[$lang];
    }

    public static function getLangTranslationsAsArray(string $lang = ''): array
    {
        $stack = static::getLangTranslations($lang);
        $r = [];
        foreach ($stack as $key => &$value) {
            $temp =& $r;

            $path = explode('.', $key);
            foreach ($path as $key) {
                $temp = &$temp[$key];
            }
            $temp = $value;
        }
        return $r;
    }

    public static function getLangTranslationsAsArrayAsString(string $lang = ''): string
    {
        return varToPHPCode(static::getLangTranslationsAsArray($lang));
    }

    public static function setLangTranslations(string $lang, array $data): array
    {
        if (!$lang) $lang = Locale::getLangCode();
        static::$stack[$lang] = $data;
        return static::$stack[$lang];
    }

    public static function addLocalePath(string $lang, string $path): void
    {
        if (!isset(self::$paths[$lang]) || !is_array(self::$paths[$lang])) {
            self::$paths[$lang] = [];
        }
        if (!in_array($path, self::$paths, true)) {
            self::$paths[$lang][] = $path;
        }
    }

    public static function getAvailableLanguages(): array
    {
        return array_keys(self::$paths);
    }

    public static function export(): array
    {
        $languages = static::getAvailableLanguages();

        $r = [];

        foreach ($languages as $language) {
            $translations = static::getLangTranslations($language);
            $r[$language] = arrayValuesRecursiveWithKeys($translations);
        }

        return $r;
    }

    public static function getMissedTranslations(array $langFilter = []): array
    {
        $languages = static::getAvailableLanguages();

        if (count($langFilter) > 1) {
            $languagesReplacement = [];
            foreach ($languages as $language) if (in_array($language, $langFilter, true)) $languagesReplacement[] = $language;

            if (count($languagesReplacement) > 1) {
                $languages = $languagesReplacement;
            }
        }

        $r = [];

        foreach ($languages as $language) {
            $translations = static::getLangTranslations($language);
            $r[$language] = arrayValuesRecursiveWithKeys($translations);
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
                        $response[$lang][$key] = isset($r[$lang][$key]) ? trim($r[$lang][$key]) : '';
                    }
                }
            }
        }

        return $response;
    }

    public static function getTranslationsNotTranslated(): array
    {
        $languages = static::getAvailableLanguages();

        $r = [];

        foreach ($languages as $language) {
            $translations = static::getLangTranslations($language);
            $r[$language] = arrayValuesRecursiveWithKeys($translations);
        }


        $response = [];
        foreach ($languages as $language) {
            foreach ($r[$language] as $key => $value) {
                $sameValue = false;
                foreach ($languages as $lang) {
                    if ($lang !== $language) {
                        $sameValue = isset($r[$lang][$key]) && $r[$lang][$key] === $r[$language][$key];
                    }
                }

                if ($sameValue) {
                    foreach ($languages as $lang) {
                        $response[$lang][$key] = isset($r[$lang][$key]) ? trim($r[$lang][$key]) : '';
                    }
                }
            }
        }

        return $response;
    }

    private static function determineLang(string $lang = null): ?string
    {
        if ($lang !== null) return $lang;

        if (static::$lang !== null) return static::$lang;

        $languages = static::getAvailableLanguages();
        if (count($languages) > 0) {
            static::$lang = getArrayFirstPosition($languages);
        }
        return static::$lang;
    }

    public static function feedWithReferenceData(array $content, string $referenceLangKey, string $fedLangKey, string $referencedLangContentKey, string $fedLangContentKey): FeedWithReferenceDataResponse
    {
        $translations = Translations::export();
        $updatedTranslations = [];
        $skippedTranslations = [];

        foreach ($content as $row) {
            $referenceDatum = $row[$referencedLangContentKey];
            $fedDatum = $row[$fedLangContentKey];

            $originalTranslationKey = array_search($referenceDatum, $translations[$referenceLangKey]);


            if (!array_key_exists($originalTranslationKey, $translations[$referenceLangKey])) {
                $skippedTranslations[$referenceDatum] = $fedDatum;
            }
            $originalFedTranslationDatum = $translations[$fedLangKey][$originalTranslationKey];

            if ($fedDatum != $originalFedTranslationDatum) {
                $updatedTranslations[$originalTranslationKey] = $fedDatum;
            }
        }

        foreach ($updatedTranslations as $translationKey => $translationDatum) {
            Translations::set($translationKey, $translationDatum, $fedLangKey);
        }

        return new FeedWithReferenceDataResponse($updatedTranslations, $skippedTranslations);
    }

    public static function feedOnlyMissedWithReferenceData(array $content, string $referenceLangKey, string $fedLangKey, string $referencedLangContentKey, string $fedLangContentKey): FeedWithReferenceDataResponse
    {
        $translations = Translations::getMissedTranslations([$referenceLangKey, $fedLangKey]);
        $updatedTranslations = [];
        $skippedTranslations = [];

        foreach ($content as $row) {
            $referenceDatum = $row[$referencedLangContentKey];
            $fedDatum = $row[$fedLangContentKey];

            $originalTranslationKey = array_search($referenceDatum, $translations[$referenceLangKey]);

            if (!array_key_exists($originalTranslationKey, $translations[$referenceLangKey])) {
                $skippedTranslations[$referenceDatum] = $fedDatum;
            }
            $originalFedTranslationDatum = $translations[$fedLangKey][$originalTranslationKey];

            if ($fedDatum != $originalFedTranslationDatum) {
                $updatedTranslations[$originalTranslationKey] = $fedDatum;
            }
        }

        foreach ($updatedTranslations as $translationKey => $translationDatum) {
            Translations::set($translationKey, $translationDatum, $fedLangKey);
        }

        return new FeedWithReferenceDataResponse($updatedTranslations, $skippedTranslations);
    }
}