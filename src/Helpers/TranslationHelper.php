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
        if (!is_array(self::$STACK[$lang])){

            $r = [];
            foreach (self::$PATHS[$lang] as $path){
                $files = scandir($path);
                foreach ($files as $file){
                    if ($file === '.' || $file === '..' || is_dir("{$path}/{$file}")){
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

    public static function addLocalePath(string $lang, string $path)
    {
        if (!is_array(self::$PATHS[$lang])){
            self::$PATHS[$lang] = [];
        }
        if (!in_array($path, self::$PATHS, true)){
            self::$PATHS[$lang][] = $path;
        }
    }
}