<?php

namespace Lkt\Translations;

use Lkt\Factory\Schemas\Schema;
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
Schema::add(require_once __DIR__ . '/Config/Schemas/lkt-i18n.php');

if (php_sapi_name() == 'cli') {
    PhinxConfigurator::addMigrationPath(__DIR__ . '/../database/migrations');
}