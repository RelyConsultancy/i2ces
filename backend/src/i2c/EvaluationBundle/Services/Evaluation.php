<?php

namespace i2c\EvaluationBundle\Services;

use Doctrine\ORM\EntityManager;
use i2c\EvaluationBundle\Entity\Evaluation as EvaluationEntity;
use JMS\Serializer\Serializer;

/**
 * Class Evaluation
 *
 * @package i2c\EvaluationBundle\Services
 */
class Evaluation
{
    /** @var Serializer */
    protected $serializer;

    protected $entityManager;

    /**
     * Evaluation constructor.
     *
     * @param Serializer    $serializer
     * @param EntityManager $entityManager
     */
    public function __construct(Serializer $serializer, EntityManager $entityManager)
    {
        $this->serializer = $serializer;

        $this->entityManager = $entityManager;
    }

    /**
     * @param EvaluationEntity $evaluation
     *
     * @return EvaluationEntity
     */
    public function updateEvaluation(EvaluationEntity $evaluation)
    {
        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();

        $this->entityManager->refresh($evaluation);

        return $evaluation;
    }
}
