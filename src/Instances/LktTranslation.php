<?php

namespace Lkt\Translations\Instances;

use Lkt\Translations\Enums\TranslationType;
use Lkt\Translations\Generated\GeneratedLktTranslation;

class LktTranslation extends GeneratedLktTranslation
{
    const COMPONENT = 'lkt-i18n';

    public static function createOrUpdate(string $property, TranslationType $type, array $value = [], int $parentId = 0): static
    {
        $query = static::getQueryCaller()->andPropertyEqual($property);
        if ($parentId > 0) $query->andParentEqual($parentId);
        $instance = static::getOne($query);
        $payload = [
            'type' => $type->value,
            'property' => $property,
            'valueData' => $value,
            'parentId' => $parentId,
        ];
        if (!$instance) {
            $instance = LktTranslation::getInstance()->autoCreate($payload);
        } else {
            $instance->autoUpdate($payload);
        }
        return $instance;
    }

    public static function createIfMissing(string $property, TranslationType $type, array $value = [], int $parentId = 0): static
    {
        $query = static::getQueryCaller()->andPropertyEqual($property);
        if ($parentId > 0) $query->andParentEqual($parentId);
        $instance = static::getOne($query);
        if (!$instance) {
            $instance = LktTranslation::getInstance()->autoCreate([
                'type' => $type->value,
                'property' => $property,
                'valueData' => $value,
                'parentId' => $parentId,
            ]);
        }
        return $instance;
    }
}