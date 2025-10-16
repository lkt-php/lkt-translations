<?php

namespace Lkt\Translations\Enums;

class TranslationType
{
    const Text = 'text';
    const Textarea = 'textarea';
    const Html = 'html';
    const Many = 'many';
//    const Select = 'select';

    const Types = [
        self::Text,
        self::Textarea,
        self::Html,
        self::Many,
//        self::Select,
    ];
}