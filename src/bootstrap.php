<?php

namespace Lkt\Translations;

function __(string $key = '', string $lang = null)
{
    return Translations::get($key, $lang);
}

function addLocalePath(string $lang, string $path): void
{
    Translations::addLocalePath($lang, $path);
}

