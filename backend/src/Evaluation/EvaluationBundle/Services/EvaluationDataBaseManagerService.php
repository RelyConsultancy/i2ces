<?php

namespace Evaluation\EvaluationBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

/**
 * Class EvaluationDataBaseManagerService
 *
 * @package Evaluation\EvaluationBundle\Services
 */
class EvaluationDataBaseManagerService
{
    /** @var AclHelper  */
    protected $aclHelper;

    /** @var EntityManager  */
    protected $entityManager;

    /**
     * EvaluationDataBaseManagerService constructor.
     *
     * @param AclHelper $aclHelper
     * @param Registry  $registry
     */
    public function __construct(AclHelper $aclHelper, Registry $registry)
    {
        $this->aclHelper = $aclHelper;
        $this->entityManager = $registry->getEntityManager();
    }

    /**
     * @param array $uids
     *
     * @return mixed
     */
    public function getByUids($uids)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
            ->from('EvaluationEvaluationBundle:Evaluation', 'e')
            ->where($queryBuilder->expr()->in('e.uid', '?1'))
            ->setParameter(1, $uids);


        $query = $this->aclHelper->apply($queryBuilder, 'VIEW');

        $result = $query->execute();

        return $result;
    }

    /**
     * @param array $uid
     *
     * @return mixed
     */
    public function getByUid($uid)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
            ->from('EvaluationEvaluationBundle:Evaluation', 'e')
            ->where($queryBuilder->expr()->eq('e.uid', '?1'))
            ->setParameter(1, $uid);

        $query = $this->aclHelper->apply($queryBuilder, 'VIEW');

        $result = $query->getOneOrNullResult();

        return $result;
    }

    /**
     * @param array $uid
     *
     * @return mixed
     */
    public function getByUidForEditing($uid)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
            ->from('EvaluationEvaluationBundle:Evaluation', 'e')
            ->where($queryBuilder->expr()->eq('e.uid', '?1'))
            ->setParameter(1, $uid);

        $query = $this->aclHelper->apply($queryBuilder, 'EDIT');

        $result = $query->getOneOrNullResult();

        return $result;
    }
}
