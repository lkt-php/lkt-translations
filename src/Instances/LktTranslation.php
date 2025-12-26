<?php

namespace Lkt\Translations\Instances;

use Lkt\Factory\Schemas\Schema;
use Lkt\Translations\Generated\GeneratedLktTranslation;

class LktTranslation extends GeneratedLktTranslation
{
    const COMPONENT = 'lkt-i18n';

    public function read(): array
    {
        $fields = Schema::get(static::COMPONENT)->getAllFields();
        return $this->readFields($fields);
    }

    public function doCreate(array $data): static
    {
        static::feedInstance($this, $data, 'create');
        return $this->save();
    }

    public function doUpdate(array $data): static
    {
        static::feedInstance($this, $data, 'update');
        return $this->save();
    }

    public static function createOrUpdate(string $property, string $type, array $value = [], int $parentId = 0): static
    {
        $query = static::getQueryCaller()->andPropertyEqual($property);
        if ($parentId > 0) $query->andParentEqual($parentId);
        $instance = static::getOne($query);
        $payload = [
            'type' => $type,
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

    public static function createIfMissing(string $property, string $type, array $value = [], int $parentId = 0): static
    {
        $query = static::getQueryCaller()->andPropertyEqual($property);
        if ($parentId > 0) $query->andParentEqual($parentId);
        $instance = static::getOne($query);
        if (!$instance) {
            $instance = LktTranslation::getInstance()->autoCreate([
                'type' => $type,
                'property' => $property,
                'valueData' => $value,
                'parentId' => $parentId,
            ]);
        }
        return $instance;
    }
}