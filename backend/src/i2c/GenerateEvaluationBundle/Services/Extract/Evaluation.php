<?php

namespace i2c\GenerateEvaluationBundle\Services\Extract;

use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class Evaluation
 *
 * @package i2c\GenerateEvaluationBundle\Services\Extract
 */
class Evaluation implements ExtractInterface
{
    /**
     * Calls all the function in the class that begin with "get"
     *
     * @param string $cid
     *
     * @return array
     */
    public function fetchAll($cid)
    {
        // TODO: Implement fetchAll() method.
    }

    public function getGeneralData()
    {

    }
}
