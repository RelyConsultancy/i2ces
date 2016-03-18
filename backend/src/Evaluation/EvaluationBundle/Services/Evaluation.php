<?php

namespace Evaluation\EvaluationBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Evaluation\EvaluationBundle\Entity\Evaluation as EvaluationEntity;
use JMS\Serializer\Serializer;

/**
 * Class Evaluation
 *
 * @package Evaluation\EvaluationBundle\Services
 */
class Evaluation
{
    /** @var Serializer */
    protected $serializer;

    protected $entityManager;

    /**
     * Evaluation constructor.
     *
     * @param Serializer $serializer
     * @param Registry   $registry
     */
    public function __construct(Serializer $serializer, Registry $registry)
    {
        $this->serializer = $serializer;

        $this->entityManager = $registry->getEntityManager();
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
