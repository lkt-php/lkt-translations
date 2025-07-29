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
}