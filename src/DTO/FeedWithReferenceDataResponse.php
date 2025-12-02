<?php

namespace Lkt\Translations\DTO;

class FeedWithReferenceDataResponse
{
    public array $updated = [];
    public array $skipped = [];

    public function __construct(array $updated, array $skipped)
    {
        $this->updated = $updated;
        $this->skipped = $skipped;
    }
}