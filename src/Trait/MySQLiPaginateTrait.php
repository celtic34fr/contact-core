<?php

namespace Celtic34fr\ContactCore\Trait;

use JetBrains\PhpStorm\ArrayShape;

trait MySQLiPaginateTrait
{
    #[ArrayShape(['datas' => "array", 'pages' => "float", 'page' => "int|mixed"])]
    private function paginateMSI($stmt, $currentPageNo = 1, $limit = 10): array
    {
        $offset = $limit * $currentPageNo - 1;
        $mysqliPage = array_splice($stmt, $offset, $limit);

        return [
            'datas' => $mysqliPage,
            'pages' => ceil(sizeof($stmt)/$limit),
            'page' => $currentPageNo,
        ];
    }
}
