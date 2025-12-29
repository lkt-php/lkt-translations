<?php

namespace Lkt\Translations\Enums;

enum TranslationType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Html = 'html';
    case Many = 'many';

    public static function getChoiceOptions(): array
    {
        return [
            'text' => TranslationType::Text->value,
            'textarea' => TranslationType::Textarea->value,
            'html' => TranslationType::Html->value,
            'many' => TranslationType::Many->value,
        ];
    }
}