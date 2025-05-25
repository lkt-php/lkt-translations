<?php

namespace Lkt\WebPages\Config\Schemas;

use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\StringChoiceField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Translations\Enums\TranslationType;
use Lkt\Translations\Instances\LktTranslation;

return Schema::table('lkt_i18n', LktTranslation::COMPONENT)
    ->setInstanceSettings(
        InstanceSettings::define(LktTranslation::class)
            ->setNamespaceForGeneratedClass('Lkt\Translations\Generated')
            ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
    )
    ->setItemsPerPage(20)
    ->setCountableField('id')
    ->setExcludedFieldsForViewFeed('create', ['value'])
    ->setExcludedFieldsForViewFeed('update', ['value'])
    ->addField(IdField::define('id'))
    ->addField(
        DateTimeField::define('createdAt', 'created_at')
            ->setDefaultReadFormat('Y-m-d')
            ->setCurrentTimeStampAsDefaultValue()
    )
    ->addField(
        DateTimeField::define('updatedAt', 'updated_at')
            ->setDefaultReadFormat('Y-m-d')
            ->setCurrentTimeStampAsDefaultValue()
    )
    ->addField(StringChoiceField::choice(TranslationType::Types, 'type'))
    ->addField(StringField::define('property')->setIsUnique(true))

    ->addField(StringField::define('value')->setIsI18nJson())

    ->addField(AssocJSONField::define('valueData', 'value')->setIsI18nJson())
    ;