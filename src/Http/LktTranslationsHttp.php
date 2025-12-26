<?php

namespace Lkt\Translations\Http;

use Lkt\Factory\Schemas\Exceptions\DuplicatedValueException;
use Lkt\Http\Response;
use Lkt\Translations\Instances\LktTranslation;
use Lkt\Translations\Translations;
use function Lkt\Tools\Parse\clearInput;

class LktTranslationsHttp
{
    public static function index(array $params): Response
    {
        $queryBuilder = LktTranslation::getQueryCaller()
            ->andParentEqual(0);

        if (isset($params['type'])) {
            $type = clearInput($params['type']);
            $queryBuilder->andTypeEqual($type);
        } else {
            $queryBuilder->andTypeNot('many');
        }

        if (isset($params['property'])) {
            $property = clearInput($params['property']);
            if ($property !== '') $queryBuilder->andPropertyLike($property);
        }

//        if (isset($params['value'])) {
//            $value = clearInput($params['value']);
//            if ($value !== '') $queryBuilder->andValueLike($value);
//        }

        if (isset($params['page'])) {
            $page = (int)clearInput($params['page']);

            if (isset($params['itemsPerPage'])) {
                $itemsPerPage = (int)clearInput($params['itemsPerPage']);
                $queryBuilder->pagination($page, $itemsPerPage);
            }

            $results = LktTranslation::getPage($page, $queryBuilder);
        } else {
            $results = LktTranslation::getMany($queryBuilder);
        }


        $response = [];
        foreach ($results as $result) $response[] = $result->read();

        return Response::ok([
            'results' => $response,
            'perms' => ['create']
        ]);
    }

    public static function i18n(array $params): Response
    {
        $results = LktTranslation::getMany(LktTranslation::getQueryCaller()->andParentEqual(0));
        $r = [];

        function processResult(LktTranslation $result, &$r)
        {
            $property = trim($result->getProperty());
            $isMany = $result->typeIsMany();
            if (str_contains($property, '.')) {
                $properties = explode('.', $property);

                $l = count($properties) - 1;
                $i = 0;
                $temp = &$r;
                while ($i <= $l) {
                    if ($i === $l) {
                        if ($isMany) {
                            $items = $result->getChildren();
                            $temp[$properties[$i]] = [];
                            foreach ($items as $item) processResult($item, $temp[$properties[$i]]);
                        } else {
                            $temp[$properties[$i]] = $result->getValue();
                        }
                        break;
                    } else {
                        if (!isset($temp[$properties[$i]])) {
                            $temp[$properties[$i]] = [];
                        }
                        $temp = &$temp[$properties[$i]];
                        ++$i;
                    }
                }

            } else {
                if ($isMany) {
                    $items = $result->getChildren();
                    $r[$property] = [];
                    foreach ($items as $item) processResult($item, $r[$property]);
                } else {
                    $r[$property] = $result->getValue();
                }
            }
        }

        foreach ($results as $result) {
            processResult($result, $r);
        }


        $codedTranslations = Translations::getLangTranslations();

        $r = [...$codedTranslations, ...$r];

        return Response::ok($r)->setJSONEncodingFlag(JSON_FORCE_OBJECT);
    }


    public static function create(array $params): Response
    {
        $instance = LktTranslation::getInstance();
        try {
            $instance->doCreate($params);

        } catch (DuplicatedValueException $e) {
            return Response::badRequest([
                'error' => $e->getMessage()
            ]);
        }

        return Response::ok([
            'item' => $instance->autoRead(),
            'id' => $instance->getId(),
        ]);
    }

    public static function read(array $params): Response
    {
        $instance = LktTranslation::getInstance((int)$params['id']);
        if ($instance->isAnonymous()) return Response::notFound();

        return Response::ok([
            'item' => $instance->autoRead(),
            'perms' => ['update', 'drop', 'switch-edit-mode']
        ]);
    }

    public static function update(array $params): Response
    {
        $instance = LktTranslation::getInstance((int)$params['id']);
        if ($instance->isAnonymous()) return Response::notFound();
        $instance->doUpdate($params);

        return Response::ok([
            'id' => $instance->getId(),
        ]);
    }

    public static function drop(array $params): Response
    {
        $instance = LktTranslation::getInstance((int)$params['id']);
        if ($instance->isAnonymous()) return Response::notFound();
        $instance->delete();

        return Response::ok();
    }
}