<?php

namespace Celtic34fr\ContactCore\Trait;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use JetBrains\PhpStorm\ArrayShape;

trait DbPaginateTrait
{

    #[ArrayShape(['datas' => "\Doctrine\ORM\Tools\Pagination\Paginator", 'pages' => "int|null", 'page' => "int|mixed"])]
    private function paginateDoctrine(Query $dql, int $currentPageNo = 1, int $limit = 10): array
    {
        if ($currentPageNo > 0 && $limit > 0) {
            $paginator = new Paginator($dql);
            $paginator->getQuery()
                ->setFirstResult($limit * ($currentPageNo - 1)) // Offset
                ->setMaxResults($limit) // Limit
            ;
            $datas = $paginator;
            $pages = ceil($paginator->count()/$limit);
            $page = $currentPageNo;
        } else {
            $datas = $dql->getResult();
            $pages = 0;
            $page = 0;
        }
        return [
            'datas' => $datas,
            'pages' => $pages,
            'page' => $page,
        ];
    }

    /**
     * @param array $results
     * @param int $currentPageNo
     * @param int $limit
     * @return array
     */
    #[ArrayShape(['datas' => "array", 'pages' => "float", 'page' => "int"])]
    private function paginateManual(array $results, int $currentPageNo = 1, int $limit = 10): array
    {
        $datas = $results;
        $pages = ceil(sizeof($results) / $limit);
        if (!empty($results) && $currentPageNo > 0 && $limit > 0) {
            $datas = array_slice($results, $limit * ($currentPageNo - 1), $limit);
        }
        return [
            'datas' => $datas,
            'pages' => $pages,
            'page' => $currentPageNo,
        ];
    }

}