<?php

namespace i2c\EvaluationBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\Evaluation;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

/**
 * Class EvaluationDataBaseManager
 *
 * @package i2c\EvaluationBundle\Services
 */
class EvaluationDataBaseManager
{
    /** @var AclHelper */
    protected $aclHelper;

    /** @var EntityManager */
    protected $entityManager;

    /**
     * EvaluationDataBaseManager constructor.
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
     * @return mixed
     */
    public function getAllForViewing()
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from('i2cEvaluationBundle:Evaluation', 'e')
                     ->where(
                         $queryBuilder->expr()->in('e.state', '?1')
                     )
                     ->setParameter(1, [Evaluation::STATE_DRAFT, Evaluation::STATE_PUBLISHED]);

        $query = $this->aclHelper->apply($queryBuilder, 'VIEW');

        $result = $query->execute();

        return $result;
    }

    /**
     * @return mixed
     */
    public function getAllForEditing()
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from('i2cEvaluationBundle:Evaluation', 'e')
                     ->where($queryBuilder->expr()->eq('e.state', '?1'))
                     ->setParameter(1, Evaluation::STATE_DRAFT);

        $query = $this->aclHelper->apply($queryBuilder, 'EDIT');

        $result = $query->execute();

        return $result;
    }

    /**
     * @param array $uids
     *
     * @return mixed
     */
    public function getByCids($uids)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from('i2cEvaluationBundle:Evaluation', 'e')
                     ->where($queryBuilder->expr()->in('e.cid', '?1'))
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
    public function getByCid($uid)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from('i2cEvaluationBundle:Evaluation', 'e')
                     ->where($queryBuilder->expr()->eq('e.cid', '?1'))
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
    public function getByCidForEditing($uid)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from('i2cEvaluationBundle:Evaluation', 'e')
                     ->where($queryBuilder->expr()->eq('e.cid', '?1'))
                     ->setParameter(1, $uid);

        $query = $this->aclHelper->apply($queryBuilder, 'EDIT');

        $result = $query->getOneOrNullResult();

        return $result;
    }
}
