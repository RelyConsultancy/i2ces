<?php

namespace i2c\PageBundle\Services;

use Doctrine\ORM\EntityManager;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

/**
 * Class PageDatabaseManager
 *
 * @package i2c\PageBundle\Services
 */
class PageDatabaseManager
{
    /** @var AclHelper */
    protected $aclHelper;

    /** @var EntityManager */
    protected $entityManager;

    /**
     * PageDatabaseManager constructor.
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
     * @param string $type
     *
     * @return mixed
     */
    public function getPageForEditing($type)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
            ->from('i2cPageBundle:Page', 'e')
            ->where($queryBuilder->expr()->eq('e.type', '?1'))
            ->setParameter(1, $type);

        $query = $this->aclHelper->apply($queryBuilder, 'EDIT');

        $result = $query->getOneOrNullResult();

        return $result;
    }

    /**
     * @param string $type
     *
     * @return mixed
     */
    public function getPageForViewing($type)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
            ->from('i2cPageBundle:Page', 'e')
            ->where($queryBuilder->expr()->eq('e.type', '?1'))
            ->setParameter(1, $type);

        $query = $this->aclHelper->apply($queryBuilder, 'VIEW');

        $result = $query->getOneOrNullResult();

        return $result;
    }
}
