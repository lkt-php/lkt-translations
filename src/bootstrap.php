<?php

namespace Lkt\Translations;

/**
 * @param string $key
 * @param string|null $lang
 * @return mixed
 */
function __(string $key = '', string $lang = null)
{
    return Translations::get($key, $lang);
}

/**
 * @param string $lang
 * @param string $path
 * @return void
 */
function addLocalePath(string $lang, string $path): void
{
    Translations::addLocalePath($lang, $path);
}

