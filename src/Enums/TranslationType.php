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

//
//class TranslationType
//{
//    const Text = 'text';
//    const Textarea = 'textarea';
//    const Html = 'html';
//    const Many = 'many';
////    const Select = 'select';
//
//    const Types = [
//        self::Text,
//        self::Textarea,
//        self::Html,
//        self::Many,
////        self::Select,
//    ];
//}