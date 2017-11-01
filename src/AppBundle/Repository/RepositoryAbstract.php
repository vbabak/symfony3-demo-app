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
        $connection = $this->getEntityManager()->getConnection();
        $where = '';
        $where_cnt = 1;
        foreach ($criteria as $k => $v) {
            if ($where) {
                $where .= ' AND';
            }
            $comparator = '=';
            if (is_array($v)) {
                $comparator = $v['comparator'];
                $v = $v['value'];
            }
            $where .= " e." . $connection->quote($k) . " $comparator ?$where_cnt";
            $where_cnt++;
        }

        $q_total = "SELECT COUNT(e) FROM " . $this->getClassName() . " e";
        if ($where) {
            $q_total .= ' WHERE ' . $where;
        }
        $query_total = $this->getEntityManager()->createQuery($q_total);
        $where_cnt = 1;
        foreach ($criteria as $k => $v) {
            if (is_array($v)) {
                $v = $v['value'];
            }
            $query_total->setParameter($where_cnt, $v);
            $where_cnt++;
        }

        $count = (int)$query_total->getSingleScalarResult();

        $q_items = "SELECT e FROM " . $this->getClassName() . " e";
        if ($where) {
            $q_items .= ' WHERE ' . $where;
        }
        if ($orderBy) {
            $order = '';
            foreach ($orderBy as $k => $v) {
                if ($order) {
                    $order .= ', ';
                }
                $order .= "e." . $connection->quote($k) . " " . trim($connection->quote($v), "'");
            }
            if ($order) {
                $q_items .= ' ORDER BY ' . $order;
            }
        }
        $query_items = $this->getEntityManager()->createQuery($q_items);
        $where_cnt = 1;
        foreach ($criteria as $k => $v) {
            if (is_array($v)) {
                $v = $v['value'];
            }
            $query_items->setParameter($where_cnt, $v);
            $where_cnt++;
        }

        $paginationService->setTotalElements($count);
        $max_result = $paginationService->getPerPage();
        $query_items->setMaxResults($max_result);
        $offset = $paginationService->getOffset();
        $query_items->setFirstResult($offset);

        $items = $query_items->getResult();

        $links_range = $paginationService->getLinksRange();

        return [
            'items' => $items,
            'links_range' => $links_range,
            'current_page' => $paginationService->getCurrentPage(),
            'total_pages' => $paginationService->getTotalPages(),
        ];
    }
}