<?php

declare(strict_types = 1);

namespace AppBundle\Utils\Pagination\PaginationInterface;

interface PaginationInterface extends PaginationParamsInterface
{
    public function getLinksRange(): array;
}
