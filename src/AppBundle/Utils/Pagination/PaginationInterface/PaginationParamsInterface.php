<?php

declare(strict_types = 1);

namespace AppBundle\Utils\Pagination\PaginationInterface;

interface PaginationParamsInterface
{
    public function setCurrentPage(int $page);

    public function getCurrentPage(): int;

    public function setPerPage(int $page);

    public function getPerPage(): int;

    public function setTotalElements(int $total);

    public function getTotalElements(): int;

    public function setNumLinks(int $links);

    public function getNumLinks(): int;

    public function getOffset(): int;

    public function getTotalPages(): int;
}
