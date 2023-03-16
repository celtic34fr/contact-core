<?php

namespace Celtic34fr\ContactCore\Trait;

use Doctrine\ORM\Tools\Pagination\Paginator;
use JetBrains\PhpStorm\ArrayShape;

trait DbPaginateTrait
{

    #[ArrayShape(['datas' => "\Doctrine\ORM\Tools\Pagination\Paginator", 'pages' => "int|null", 'page' => "int|mixed"])]
    private function paginateDoctrine($dql, $currentPageNo = 1, $limit = 10): array
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($currentPageNo - 1)) // Offset
            ->setMaxResults($limit) // Limit
        ;
        return [
            'datas' => $paginator,
            'pages' => ceil($paginator->count()/$limit),
            'page' => $currentPageNo,
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