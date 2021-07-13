<?php


namespace Lkt\Translations\Helpers;

/**
 * Class TranslationHelper
 *
 * @package Lkt\Translations\Helpers
 */
class TranslationHelper
{
    protected static $STACK = [];
    protected static $PATHS = [];

    /**
     * @param string $lang
     * @return array
     */
    public static function getLangTranslations(string $lang): array
    {
        if (!is_array(self::$STACK[$lang])) {

            $r = [];
            foreach (self::$PATHS[$lang] as $path) {
                $files = scandir($path);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..' || is_dir("{$path}/{$file}")) {
                        continue;
                    }

                    $data = require "{$path}/{$file}";
                    $r = array_merge($r, $data);
                }
            }

            self::$STACK[$lang] = $r;
        }

        return self::$STACK[$lang];
    }

    /**
     * @param string $lang
     * @param string $path
     */
    public static function addLocalePath(string $lang, string $path): void
    {
        if (!isset(self::$PATHS[$lang]) || !is_array(self::$PATHS[$lang])) {
            self::$PATHS[$lang] = [];
        }
        if (!in_array($path, self::$PATHS, true)) {
            self::$PATHS[$lang][] = $path;
        }
    }

    /**
     * @return array
     */
    public static function getAvailableLanguages(): array
    {
        return array_keys(self::$PATHS);
    }

    /**
     * @param array $array
     * @param array $parentKeys
     * @return array
     */
    public function arrayValuesRecursive($array = [], $parentKeys = [])
    {
        $r = [];

        foreach ($array as $value) {
            if (is_array($value)) {
                $r = array_merge($r, self::arrayValuesRecursive($value));
            } else {
                $r[] = $value;
            }
        }
        return $r;
    }

    /**
     * @param array $array
     * @param string $divider
     * @param array $parentKeys
     * @return array
     */
    public function arrayValuesRecursiveWithKeys($array = [], $divider = '.', $parentKeys = [])
    {
        $r = [];

        foreach ($array as $key => $value) {
            $t = array_merge($parentKeys, [$key]);
            if (is_array($value)) {
                $r = array_merge($r, self::arrayValuesRecursiveWithKeys($value, $divider, $t));
            } else {
                $k = implode($divider, $t);
                $r[$k] = $value;
            }
        }
        return $r;
    }
}