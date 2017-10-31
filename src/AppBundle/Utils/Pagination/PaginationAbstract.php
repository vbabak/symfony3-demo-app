<?php

declare(strict_types = 1);

namespace AppBundle\Utils\Pagination;

use AppBundle\Utils\Pagination\PaginationInterface\PaginationParamsInterface;

abstract class PaginationAbstract implements PaginationParamsInterface
{
    /** @var int */
    protected $current_page = 1;

    /** @var int */
    protected $per_page = 10;

    /** @var int */
    protected $total_elements = 0;

    /** @var int */
    protected $num_links = 7;

    /**
     * PaginationAbstract constructor.
     *
     * @param int $per_page
     * @param int $display_links
     */
    public function __construct(int $per_page, int $display_links)
    {
        $this->setPerPage($per_page);
        $this->setNumLinks($display_links);
    }

    /**
     * @param int $page
     *
     * @return $this
     */
    public function setCurrentPage(int $page)
    {
        $this->current_page = $page;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->current_page;
    }

    /**
     * @param int $page
     *
     * @return $this
     */
    public function setPerPage(int $page)
    {
        $this->per_page = $page;

        return $this;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->per_page;
    }

    /**
     * @param int $total
     *
     * @return $this
     */
    public function setTotalElements(int $total)
    {
        $this->total_elements = $total;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalElements(): int
    {
        return $this->total_elements;
    }

    /**
     * @param int $links
     *
     * @return $this
     */
    public function setNumLinks(int $links)
    {
        $this->num_links = $links;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumLinks(): int
    {
        return $this->num_links;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        $offset = $this->getCurrentPage() * $this->getPerPage() - $this->getPerPage();
        if ($offset < 0) {
            $offset = 0;
        } else if ($offset > $this->getTotalPages()) {
            $offset = $this->getTotalElements();
        }

        return $offset;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        $total_pages = floor($this->getTotalElements() / $this->getPerPage());
        $total_pages = max(1, $total_pages);

        return (int)$total_pages;
    }
}