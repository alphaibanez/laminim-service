<?php

namespace Lkt\Tools\Pagination;

function getTotalPages(int $amountOfItems, int $itemsPerPage): int
{
    return (int)ceil($amountOfItems / $itemsPerPage);
}