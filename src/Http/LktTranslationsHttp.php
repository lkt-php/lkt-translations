<?php

namespace Lkt\Translations\Http;

use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
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
        foreach ($results as $result) $response[] = $result->autoRead();

        return Response::ok([
            'results' => $response,
            'maxPage' => LktTranslation::getAmountOfPages($queryBuilder),
            'perms' => ['create']
        ]);
    }

    public static function i18n(array $params): Response
    {
        return Response::ok(Translations::getCombinedLangStack())->setJSONEncodingFlag(JSON_FORCE_OBJECT);
    }


    public static function create(array $params): Response
    {
        $instance = LktTranslation::getInstance();
        try {
            $instance->setAccessPolicy('write', AccessPolicyEndOfLife::UntilNextWrite)->autoCreate($params);

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
        $instance->setAccessPolicy('write', AccessPolicyEndOfLife::UntilNextWrite)->autoUpdate($params);

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