<?php

namespace Lkt\Translations\Http;

use Lkt\Factory\Schemas\Exceptions\DuplicatedValueException;
use Lkt\Http\Response;
use Lkt\Translations\Instances\LktTranslation;
use function Lkt\Tools\Parse\clearInput;

class LktTranslationsHttp
{
    public static function index(array $params): Response
    {
        $queryBuilder = LktTranslation::getQueryCaller();

        if (isset($params['type'])) {
            $type = (int)clearInput($params['type']);
            $queryBuilder->andTypeEqual($type);
        }

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
            'item' => $instance->read(),
            'id' => $instance->getId(),
        ]);
    }

    public static function read(array $params): Response
    {
        $instance = LktTranslation::getInstance((int)$params['id']);
        if ($instance->isAnonymous()) return Response::notFound();

        return Response::ok([
            'item' => $instance->read(),
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