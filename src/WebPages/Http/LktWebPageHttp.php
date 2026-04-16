<?php

namespace Lkt\WebPages\Http;

use Lkt\Http\Request;
use Lkt\Http\Response;
use Lkt\Instances\LktWebPage;
use Lkt\Instances\LktWebPageMetas;
use function Lkt\Tools\Parse\clearInput;

class LktWebPageHttp
{
    public static function index(array $params): Response
    {
        $queryBuilder = LktWebPage::getQueryCaller();

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

            $results = LktWebPage::getPage($page, $queryBuilder);
        } else {
            $results = LktWebPage::getMany($queryBuilder);
        }


        $response = [];
        foreach ($results as $result) $response[] = $result->autoRead();

        return Response::ok([
            'results' => $response,
            'perms' => ['create'],
            'maxPage' => 1,
        ]);
    }
    public static function create(array $params): Response
    {
        $instance = LktWebPage::getInstance();
        $instance->autoCreate($params);

        return Response::ok([
            'item' => $instance->autoRead(),
            'id' => $instance->getId(),
        ]);
    }

    public static function read(array $params): Response
    {
        $instance = LktWebPage::getInstance((int)$params['id']);
        if ($instance->isAnonymous()) return Response::notFound();

        return Response::ok([
            'item' => $instance->autoRead(),
            'perms' => ['update', 'drop', 'switch-edit-mode']
        ]);
    }

    public static function view(Request $request): Response
    {
        $slug = LktWebPageMetas::fromSlug($request->params['slug']);

        if (!$slug) return Response::notFound();

        if ($slug->getWebPageId() > 0) {
            $webPage = $slug->getWebPage();
            return Response::ok([
                'item' => $webPage->setAccessPolicy('public-read')->autoRead(),
                'type' => 'page',
                'pageType' => $webPage->getType(),
            ]);
        }

        if ($slug->getWebCategoryId() > 0) {
            return Response::ok([
                'item' => $slug->getWebCategory()->autoRead(),
                'results' => [],
                'maxPage' => 1,
                'type' => 'category',
            ]);
        }

        return Response::notFound();
    }

    public static function children(array $params): Response
    {
        $instance = LktWebPage::getInstance((int)$params['id']);
        if ($instance->isAnonymous()) return Response::notFound();

        return Response::ok([
            'results' => $instance->autoRead()['webElements'],
            'perms' => ['update']
        ]);
    }

    public static function update(Request $request): Response
    {
        $instance = LktWebPage::getInstance((int)$request->params['id']);
        if ($instance->isAnonymous()) return Response::notFound();
        $instance->autoUpdate($request->params);

        return Response::ok([
            'id' => $instance->getId(),
        ]);
    }

    public static function drop(array $params): Response
    {
        $instance = LktWebPage::getInstance((int)$params['id']);
        if ($instance->isAnonymous()) return Response::notFound();
        $instance->delete();

        return Response::ok();
    }
}