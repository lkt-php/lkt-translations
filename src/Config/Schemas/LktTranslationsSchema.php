<?php

namespace Lkt\WebPages\Config\Schemas;

use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\StringChoiceField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Translations\Enums\TranslationType;
use Lkt\Translations\Generated\LktTranslationOrderBy;
use Lkt\Translations\Instances\LktTranslation;

Schema::add(
    Schema::table('lkt_i18n', LktTranslation::COMPONENT)
        ->setInstanceSettings(
            InstanceSettings::define(LktTranslation::class)
                ->setNamespaceForGeneratedClass('Lkt\Translations\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'property' => 'label',
            'id',
            'property',
            'type',
            'value',
            'valueData',
        ])
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
        ->addField(StringChoiceField::choice(TranslationType::getChoiceOptions(), 'type'))
        ->addField(StringField::define('property'))
        ->addField(StringField::define('value')->setIsI18nJson())
        ->addField(AssocJSONField::define('valueData', 'value')->setIsI18nJson())
        ->addField(ForeignKeyField::defineRelation(LktTranslation::COMPONENT, 'parent', 'parent_id'))
        ->addField(RelatedField::defineRelation(LktTranslation::COMPONENT, 'children', 'parent_id')->setOrder(LktTranslationOrderBy::propertyASC()))
        ->addAccessPolicy('write', ['type', 'property', 'valueData', 'parent', 'children'])
);