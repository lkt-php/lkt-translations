<?php

namespace Lkt\Translations;

use Lkt\Phinx\PhinxConfigurator;

function __(string $key = '', string $lang = null)
{
    return Translations::get($key, $lang);
}

function addLocalePath(string $lang, string $path): void
{
    Translations::addLocalePath($lang, $path);
}

/**
 * Load Schemas
 */
require_once __DIR__ . '/Config/Schemas/LktTranslationsSchema.php';

if (php_sapi_name() == 'cli') {
    PhinxConfigurator::addMigrationPath(__DIR__ . '/../database/migrations');
}