<?php

namespace Lkt\Translations;

use Lkt\Translations\Helpers\TranslationHelper;

function addLocalePath(string $lang, string $path): void
{
    TranslationHelper::addLocalePath($lang, $path);
}