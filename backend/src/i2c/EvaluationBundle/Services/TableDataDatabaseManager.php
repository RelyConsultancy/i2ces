<?php

namespace i2c\EvaluationBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\TableData;

/**
 * Class TableDataDatabaseManager
 *
 * @package i2c\EvaluationBundle\Services
 */
class TableDataDatabaseManager
{
    /** @var EntityManager */
    protected $entityManager;

    /**
     * EvaluationDataBaseManagerService constructor.
     *
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->entityManager = $registry->getEntityManager();
    }

    /**
     * @param string $cid
     * @param string $id
     *
     * @return TableData
     */
    public function getTableData($cid, $id)
    {
        $tableData = $this->entityManager->getRepository('i2cEvaluationBundle:TableData')
                                         ->findOneBy(['cid' => $cid, 'id' => $id]);

        return $tableData;
    }
}
