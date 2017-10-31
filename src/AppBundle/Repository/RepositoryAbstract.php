<?php

declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Utils\Pagination\PaginationInterface\PaginationInterface;

class RepositoryAbstract extends \Doctrine\ORM\EntityRepository
{
    /**
     * Function to paginate results
     *
     * @param PaginationInterface $paginationService
     * @param array $criteria Example: ['id' => 1] or ['id' => ['value' => 1, 'comparator' => '>']]
     * @param array|null $orderBy
     *
     * @return array
     */
    public function paginate(PaginationInterface $paginationService, array $criteria, array $orderBy = null): array
    {
        $where = '';
        foreach ($criteria as $k => $v) {
            if ($where) {
                $where .= ' AND';
            }
            $comparator = '=';
            if (is_array($v)) {
                $comparator = $v['comparator'];
                $v = $v['value'];
            }
            $where .= " e.$k $comparator :$k";
        }

        $q_total = "SELECT COUNT(e) FROM " . $this->getClassName() . " e";
        if ($where) {
            $q_total .= ' WHERE ' . $where;
        }
        $query_total = $this->getEntityManager()->createQuery($q_total);
        foreach ($criteria as $k => $v) {
            if (is_array($v)) {
                $v = $v['value'];
            }
            $query_total->setParameter($k, $v);
        }

        $count = (int)$query_total->getSingleScalarResult();

        $q_items = "SELECT e FROM " . $this->getClassName() . " e";
        if ($where) {
            $q_items .= ' WHERE ' . $where;
        }
        $query_items = $this->getEntityManager()->createQuery($q_items);
        foreach ($criteria as $k => $v) {
            if (is_array($v)) {
                $v = $v['value'];
            }
            $query_items->setParameter($k, $v);
        }
        $query_items->setMaxResults($paginationService->getPerPage());
        $query_items->setFirstResult($paginationService->getOffset());

        $items = $query_items->getResult();

        $paginationService->setTotalElements($count);
        $links_range = $paginationService->getLinksRange();

        return [
            'items' => $items,
            'links_range' => $links_range,
            'current_page' => $paginationService->getCurrentPage()
        ];
    }
}