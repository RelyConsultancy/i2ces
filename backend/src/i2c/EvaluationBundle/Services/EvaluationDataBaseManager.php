<?php

namespace i2c\EvaluationBundle\Services;

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
     * @param AclHelper     $aclHelper
     * @param EntityManager $entityManager
     */
    public function __construct(AclHelper $aclHelper, EntityManager $entityManager)
    {
        $this->aclHelper = $aclHelper;
        $this->entityManager = $entityManager;
    }

    /**
     * @return mixed
     */
    public function getAllForViewing()
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e.cid')
                     ->from('i2cEvaluationBundle:Evaluation', 'e')
                     ->where(
                         $queryBuilder->expr()->in('e.state', '?1')
                     )
                     ->setParameter(1, [Evaluation::STATE_DRAFT, Evaluation::STATE_PUBLISHED]);

        $query = $this->aclHelper->apply($queryBuilder, 'VIEW');

        $result = $query->execute();

        return array_map('current', $result);
    }

    /**
     * @return mixed
     */
    public function getAllPublishedForViewing()
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from('i2cEvaluationBundle:Evaluation', 'e')
                     ->where(
                         $queryBuilder->expr()->in('e.state', '?1')
                     )
                     ->setParameter(1, [Evaluation::STATE_PUBLISHED]);

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
        $queryBuilder->select('e.cid')
                     ->from('i2cEvaluationBundle:Evaluation', 'e')
                     ->where($queryBuilder->expr()->eq('e.state', '?1'))
                     ->setParameter(1, Evaluation::STATE_DRAFT);

        $query = $this->aclHelper->apply($queryBuilder, 'EDIT');

        $result = $query->execute();

        return array_map('current', $result);
    }

    /**
     * @param array $uids
     * @param bool  $checkPublished
     *
     * @return mixed
     */
    public function getByCids($uids, $checkPublished = false)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from('i2cEvaluationBundle:Evaluation', 'e')
                     ->where($queryBuilder->expr()->in('e.cid', '?1'))
                     ->setParameter(1, $uids);

        if ($checkPublished) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('e.state', '?2'))
                ->setParameter(2, Evaluation::STATE_PUBLISHED);
        }

        $query = $this->aclHelper->apply($queryBuilder, 'VIEW');

        $result = $query->execute();

        return $result;
    }

    /**
     * @param array $uid
     * @param bool  $checkPublished
     *
     * @return mixed
     */
    public function getByCid($uid, $checkPublished = false)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from('i2cEvaluationBundle:Evaluation', 'e')
                     ->where($queryBuilder->expr()->eq('e.cid', '?1'))
                     ->setParameter(1, $uid);

        if ($checkPublished) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('e.state', '?2'))
                         ->setParameter(2, Evaluation::STATE_PUBLISHED);
        }

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
