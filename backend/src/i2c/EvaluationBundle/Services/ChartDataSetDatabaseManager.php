<?php

namespace i2c\EvaluationBundle\Services;

use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\ChartDataSet;

/**
 * Class TableDataDatabaseManager
 *
 * @package i2c\EvaluationBundle\Services
 */
class ChartDataSetDatabaseManager
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
     * @return ChartDataSet
     */
    public function getChartDataSet($cid, $id)
    {
        $chartDataSet = $this->entityManager->getRepository('i2cEvaluationBundle:ChartDataSet')
                                         ->findOneBy(['cid' => $cid, 'id' => $id]);

        return $chartDataSet;
    }
}
