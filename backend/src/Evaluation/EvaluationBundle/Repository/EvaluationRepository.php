<?php

namespace Evaluation\EvaluationBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Evaluation\EvaluationBundle\Entity\Evaluation;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

/**
 * Class EvaluationRepository
 *
 * @package Evaluation\EvaluationBundle\Repository
 */
class EvaluationRepository extends EntityRepository
{
    /**
     * @param array     $ids
     * @param AclHelper $aclHelper
     * @param array     $permissions
     *
     * @return mixed
     */
    public function getByUids($ids, $aclHelper, $permissions)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('e', 'b')
            ->from('EvaluationEvaluationBundle:Evaluation', 'e')
            ->innerJoin('e.businessUnit', 'b')
            ->where($queryBuilder->expr()->in('e.uid', '?1'))
            ->setParameter(1, $ids);


        $query = $aclHelper->apply($queryBuilder, implode(';', $permissions));

        $result = $query->execute();

        return $result;
    }

    /**
     * @param string    $id
     * @param AclHelper $aclHelper
     * @param array     $permissions
     *
     * @return Evaluation|null
     */
    public function getByUid($id, $aclHelper, $permissions)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('e', 'b')
            ->from('EvaluationEvaluationBundle:Evaluation', 'e')
            ->innerJoin('e.businessUnit', 'b')
            ->where($queryBuilder->expr()->eq('e.uid', '?1'))
            ->setParameter(1, $id);

        $query = $aclHelper->apply($queryBuilder, implode(';', $permissions));

        $result = $query->getOneOrNullResult();

        return $result;
    }
}
