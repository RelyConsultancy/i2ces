<?php

namespace i2c\GenerateEvaluationBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Evaluation\EvaluationBundle\Entity\Chapter;
use Evaluation\EvaluationBundle\Entity\Evaluation;
use JMS\Serializer\Serializer;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class GenerateEvaluations
 *
 * @package i2c\GenerateEvaluationBundle\Services
 */
class GenerateEvaluations
{
    protected $extractServicesContainer;

    /** @var  EngineInterface */
    protected $templateRenderer;

    /** @var string  */
    protected $templatesPrefix;

    /** @var EntityManager  */
    protected $entityManager;

    /** @var  Serializer */
    protected $serializer;

    /**
     * GenerateEvaluations constructor.
     *
     * @param EngineInterface $templateRenderer
     * @param Registry        $registry
     * @param Serializer      $serializer
     * @param string          $templatesPrefix
     */
    public function __construct($templateRenderer, Registry $registry, Serializer $serializer, $templatesPrefix)
    {
        $this->templateRenderer = $templateRenderer;

        $this->templatesPrefix = $templatesPrefix;

        $this->entityManager = $registry->getEntityManager();

        $this->serializer = $serializer;
    }

    /**
     * @param ExtractInterface $extractInterface
     * @param string           $serviceName
     */
    public function addExtractService(ExtractInterface $extractInterface, $serviceName)
    {
        $this->extractServicesContainer[$serviceName] = $extractInterface;
    }

    public function generate($evaluationConfigs, $cids)
    {
        foreach ($cids as $cid) {
            $evaluationJson = $this->getJsonEntity($evaluationConfigs['twig_name'], $evaluationConfigs['data_service']);
            /** @var Evaluation $evaluation */
            $evaluation = $this->serializer->deserialize(
                $evaluationJson,
                'Evaluation\EvaluationBundle\Entity\Evaluation',
                'json'
            );
            $evaluation->setCid($cid);
            $evaluation->setState("generating");

            $chapters =[];

            foreach ($evaluationConfigs['chapters'] as $chapterConfig) {
                $additionalData = [
                    'additional_data' => $chapterConfig['additional_data']
                ];
                $chapterJson = $this->getJsonEntity(
                    $chapterConfig['twig_name'],
                    $chapterConfig['data_service'],
                    $additionalData
                );

                /** @var Chapter $chapter */
                $chapter = $this->serializer->deserialize(
                    $chapterJson,
                    'Evaluation\EvaluationBundle\Entity\Chapter',
                    'json'
                );
                $this->entityManager->persist($chapter);
                $chapters[] = $chapter;
            }

            $evaluation->setChapters($chapters);
            $this->entityManager->persist($evaluation);
            $this->entityManager->flush();

            $this->updateChaptersLocation($evaluation);

            $evaluation->setState('draft');
            $this->entityManager->persist($evaluation);

            $this->entityManager->flush();
        }
    }

    public function loadConfigData($jsonPath)
    {
        $jsonContent = file_get_contents($jsonPath);

        $config = json_decode($jsonContent, true);

        return $config;
    }

    /**
     * @param Evaluation $evaluation
     */
    protected function updateChaptersLocation(Evaluation $evaluation)
    {
        $this->entityManager->refresh($evaluation);
        /** @var Chapter $chapter */
        foreach ($evaluation->getChapters() as $chapter) {
            $this->entityManager->refresh($chapter);
            $chapter->setLocation(
                sprintf(
                    '/api/evaluations/%s/chapters/%s',
                    $evaluation->getCid(),
                    $chapter->getId()
                )
            );
            $this->entityManager->persist($chapter);
        }
    }

    /**
     * @param $serviceName
     *
     * @return array
     */
    protected function getData($serviceName)
    {
        return $this->extractServicesContainer[$serviceName]->fetchAll();
    }

    /**
     * @param string $twigName
     * @param string $serviceName
     * @param array  $additionalData
     *
     * @return string
     */
    protected function getJsonEntity($twigName, $serviceName, $additionalData = [])
    {
        $data = array_merge($this->getData($serviceName), $additionalData);

        return $this->templateRenderer->render(
            sprintf('%s/%s', $this->templatesPrefix, $twigName),
            $data
        );
    }
}
