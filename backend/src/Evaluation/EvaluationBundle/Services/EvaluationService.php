<?php

namespace Evaluation\EvaluationBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Evaluation\EvaluationBundle\Entity\Evaluation;
use JMS\Serializer\Serializer;

/**
 * Class EvaluationService
 *
 * @package Evaluation\EvaluationBundle\Services
 */
class EvaluationService
{
    /** @var Serializer */
    protected $serializer;

    protected $entityManager;

    /**
     * ChapterService constructor.
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
     * @param Evaluation $evaluation
     *
     * @return Evaluation
     */
    public function updateEvaluation(Evaluation $evaluation)
    {
        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();

        $this->entityManager->refresh($evaluation);

        return $evaluation;
    }
}
