<?php

namespace i2c\EvaluationBundle\Services;

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
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
