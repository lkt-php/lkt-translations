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

    /**
     * @param array $data
     * @param string $group
     * @param string $lang
     */
    public static function addTranslations(array $data, string $group, string $lang): void
    {
        if (!is_array(self::$STACK[$lang])){
            self::$STACK[$lang] = [];
        }
        if (!is_array(self::$STACK[$lang][$group])){
            self::$STACK[$lang][$group] = [];
        }

        self::$STACK[$lang][$group] = array_merge(self::$STACK[$lang][$group], $data);
    }

    /**
     * @param string $lang
     * @return array
     */
    public static function getLangTranslations(string $lang): array
    {
        if (!self::$STACK[$lang]){
            return [];
        }
        return self::$STACK[$lang];
    }
}