<?php

declare(strict_types = 1);

namespace AppBundle\Utils\Pagination;

use AppBundle\Utils\Pagination\PaginationInterface\PaginationInterface;

class MyPagination extends PaginationAbstract implements PaginationInterface
{
    protected $links_range = [];

    public function getLinksRange(): array
    {
        $total_pages = $this->getTotalPages();
        $next_current_page = (int)min($total_pages, $this->getCurrentPage() + 1); // this is a page in the middle of pagination
        $next_first_page = (int)max(1, $next_current_page - ceil($this->getNumLinks() / 2)); // this will be a start page of pagination
        if ($total_pages - $next_first_page < $this->getNumLinks()) {
            $next_first_page = $total_pages - $this->getNumLinks() + 1;
        }
        $next_last_page = (int)min($next_first_page + $this->getNumLinks() - 1, $total_pages);

        for ($i = $next_first_page; $i <= $next_last_page; $i++) {
            $this->links_range[] = $i;
        }

        return $this->links_range;
    }
}