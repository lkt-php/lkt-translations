<?php

namespace Lkt\Translations\Console\Commands;

use Lkt\Translations\Enums\TranslationType;
use Lkt\Translations\Instances\LktTranslation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupTranslationsCommand extends Command
{
    protected static $defaultName = 'lkt:translations:setup:i18n';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Automatically generates all default translations')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $parent = LktTranslation::createIfMissing('translationType', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('text', TranslationType::Text, [
            'es' => 'Texto',
            'en' => 'Text',
        ], $parentId);
        LktTranslation::createIfMissing('textarea', TranslationType::Text, [
            'es' => 'Área de texto',
            'en' => 'Textarea',
        ], $parentId);
        LktTranslation::createIfMissing('many', TranslationType::Text, [
            'es' => 'Diccionario',
            'en' => 'Dictionary',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('accessLevel', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('1', TranslationType::Text, [
            'es' => 'Cualquiera',
            'en' => 'Any user',
        ], $parentId);
        LktTranslation::createIfMissing('2', TranslationType::Text, [
            'es' => 'Solo usuarios registrados',
            'en' => 'Only logged users',
        ], $parentId);
        LktTranslation::createIfMissing('3', TranslationType::Text, [
            'es' => 'Solo usuarios anónimos',
            'en' => 'Only anonymous users',
        ], $parentId);
        LktTranslation::createIfMissing('4', TranslationType::Text, [
            'es' => 'Solo administradores',
            'en' => 'Only admin users',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('i18nForm', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('property', TranslationType::Text, [
            'es' => 'Propiedad',
            'en' => 'Property',
        ], $parentId);
        LktTranslation::createIfMissing('type', TranslationType::Text, [
            'es' => 'Tipo',
            'en' => 'Type',
        ], $parentId);
        LktTranslation::createIfMissing('value', TranslationType::Text, [
            'es' => 'Valor',
            'en' => 'Value',
        ], $parentId);
        LktTranslation::createIfMissing('children', TranslationType::Text, [
            'es' => 'Traducciones',
            'en' => 'Translations',
        ], $parentId);

        return 1;
    }
}