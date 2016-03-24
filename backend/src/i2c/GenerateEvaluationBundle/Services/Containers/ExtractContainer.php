<?php

namespace i2c\GenerateEvaluationBundle\Services\Containers;

use i2c\GenerateEvaluationBundle\Services\ExtractInterface;

/**
 * Class ExtractContainer
 *
 * @package i2c\GenerateEvaluationBundle\Services\Containers
 */
class ExtractContainer
{
    /** @var \ArrayObject */
    protected $extractContainer;

    /**
     * ExtractContainer constructor.
     */
    public function __construct()
    {
        $this->extractContainer = new \ArrayObject();
    }

    /**
     * @param ExtractInterface $extract
     * @param string           $serviceName
     */
    public function addExtractService(ExtractInterface $extract, $serviceName)
    {
        $this->extractContainer[$serviceName] = $extract;
    }

    /**
     * @param $serviceName
     *
     * @return ExtractInterface
     */
    public function getExtractService($serviceName)
    {
        return $this->extractContainer[$serviceName];
    }
}
