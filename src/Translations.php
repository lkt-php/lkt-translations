<?php

namespace Lkt\Translations;

use function Lkt\Tools\Arrays\arrayValuesRecursiveWithKeys;
use function Lkt\Tools\Arrays\getArrayFirstPosition;

class Translations
{
    protected static $stack = [];
    protected static $paths = [];
    protected static $lang = null;

    /**
     * @param string $lang
     * @return void
     */
    public static function setLang(string $lang)
    {
        static::$lang = $lang;
    }

    /**
     * @param string $key
     * @param string|null $lang
     * @return mixed
     */
    public static function get(string $key, string $lang = null)
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

        return $dig;
    }

    /**
     * @param string $lang
     * @return array
     */
    public static function getLangTranslations(string $lang): array
    {
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

    /**
     * @param string $lang
     * @param string $path
     */
    public static function addLocalePath(string $lang, string $path): void
    {
        if (!isset(self::$paths[$lang]) || !is_array(self::$paths[$lang])) {
            self::$paths[$lang] = [];
        }
        if (!in_array($path, self::$paths, true)) {
            self::$paths[$lang][] = $path;
        }
    }

    /**
     * @return array
     */
    public static function getAvailableLanguages(): array
    {
        return array_keys(self::$paths);
    }

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    public static function getMissedTranslations(): array
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

    /**
     * @return array
     */
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

    /**
     * @param string|null $lang
     * @return string|null
     */
    private static function determineLang(string $lang = null): ?string
    {
        if ($lang !== null) {
            return $lang;
        }

        if (static::$lang !== null) {
            return static::$lang;
        }

        $languages = static::getAvailableLanguages();
        if (count($languages) > 0) {
            static::$lang = getArrayFirstPosition($languages);
        }
        return static::$lang;
    }
}