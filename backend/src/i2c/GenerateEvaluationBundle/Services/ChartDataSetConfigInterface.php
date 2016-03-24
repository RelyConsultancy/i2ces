<?php

namespace i2c\GenerateEvaluationBundle\Services;

/**
 * Interface ChapterDiagramInterface
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
interface ChartDataSetConfigInterface
{
    /**
     * Returns an array with table config data.
     *
     * @param string $cid
     *
     * @return array
     */
    public function getTableConfig($cid);
}
