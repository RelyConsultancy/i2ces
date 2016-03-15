<?php

namespace i2c\GenerateEvaluationBundle\Services;

/**
 * Interface ExtractInterface
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
interface ExtractInterface
{
    /**
     * Calls all the function in the class that begin with "get"
     *
     * @param string $cid
     *
     * @return array
     */
    public function fetchAll($cid);
}
