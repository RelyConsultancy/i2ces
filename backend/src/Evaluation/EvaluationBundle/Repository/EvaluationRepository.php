<?php

namespace Evaluation\EvaluationBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class EvaluationRepository
 *
 * @package Evaluation\EvaluationBundle\Repository
 */
class EvaluationRepository extends EntityRepository
{
    /**
     * @param array $ids
     *
     * @return mixed
     */
    public function getByUids($ids = [])
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('e', 'b')
            ->from('EvaluationEvaluationBundle:Evaluation', 'e')
            ->innerJoin('e.businessUnit', 'b')
            ->where($queryBuilder->expr()->in('e.uid', '?1'))
            ->setParameter(1, $ids);

        $query = $queryBuilder->getQuery();

        $result = $query->execute();

        return $result;
    }
}
