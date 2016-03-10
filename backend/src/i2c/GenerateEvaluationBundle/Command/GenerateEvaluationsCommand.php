<?php

namespace i2c\GenerateEvaluationBundle\Command;

use Doctrine\ORM\EntityManager;
use Evaluation\EvaluationBundle\Entity\Chapter;
use Evaluation\EvaluationBundle\Entity\Evaluation;
use i2c\GenerateEvaluationBundle\Services\CampaignObjectiveDataService;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateEvaluationsCommand
 *
 * @package i2c\EvaluationBundle\Command
 */
class GenerateEvaluationsCommand extends ContainerAwareCommand
{
    /** @var  CampaignObjectiveDataService */
    protected $campaignDataService;
    /** @var  EntityManager */
    protected $entityManager;
    protected $campaignColorsConfig;

    public function __construct($campaignColorsConfig)
    {
        $this->campaignColorsConfig = $campaignColorsConfig;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this->setName('i2c:evaluation:generate')->setDescription(
            'This command will import the i2c data from a csv to a table'
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->campaignDataService = $this->getContainer()->get('i2c_generate_evaluation.campaign_objective_data');
        $this->entityManager = $this->getContainer()->get('doctrine')->getEntityManager();


        $campaignCids = $this->getCampaignCids();

        foreach ($campaignCids as $cid) {
            $output->writeln($this->generateEvaluation($cid));
        }

        $output->writeln("Campaigns successfully generated");
    }

    /**
     * @return string[]
     */
    protected function getCampaignCids()
    {
        return $this->campaignDataService->getCampaignCids();
    }

    /**
     * @param array $campaignData
     *
     * @return string
     */
    protected function generateEvaluation($campaignData)
    {
        $cid = $campaignData['master_campaign_id'];

        if (!is_null($this->getExistingEvaluation($cid))) {
            return "Evaluation with cid $cid already exists";
        }

        $evaluation = new Evaluation();
        $supplier = $this->getSupplier($campaignData['supplier']);

        if (is_null($supplier)) {
            return "Supplier not found for evaluation with cid $cid";
        }

        $timings = $this->campaignDataService->getTiming($cid);

        $timingPeriods = $this->getTimingsFormatted($timings);

        $campaignObjectives = $this->campaignDataService->getObjectives($cid);

        $evaluationCost = $this->campaignDataService->getEvaluationCost($cid);

        $evaluationChannels = $this->campaignDataService->getEvaluationChannels($cid);

        $evaluation->setOwner($supplier);
        $evaluation->setBusinessUnit($supplier);
        $evaluation->setCid($cid);
        $evaluation->setBrand($campaignData['brand']);
        $evaluation->setTitle($campaignData['campaign_name']);
        $evaluation->setCategory($campaignData['category']);
        $evaluation->setGeneratedAt(new \DateTime('now'));
        $evaluation->setState(Evaluation::STATE_DRAFT);
        $evaluation->setStartDate($this->getStartDate($timings));
        $evaluation->setStartDate($this->getEndDate($timings));


        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();
        $this->entityManager->refresh($evaluation);

        $templateRenderer = $this->getContainer()->get('templating');

        $mediaLaydownItems = $this->campaignDataService->getMediaLaydownRows($cid);

        $campaignObjectivesChapterContent = $templateRenderer->render(
            'EvaluationEvaluationBundle:CampaignBackground:campaign-background-version-1.json.twig',
            [
                "campaignObjectives"       => $campaignObjectives,
                "timings"                  => $timingPeriods,
                "evaluatedChannels"        => $evaluationChannels,
                "evaluatedCost"            => $evaluationCost,
                "mediaLaydownItems"        => $mediaLaydownItems,
                "mediaLaydownColorsConfig" => $this->campaignColorsConfig,
            ]
        );

        $chapters = [];

        $campaignObjectivesArray = new Chapter();
        $campaignObjectivesArray->setContent(json_encode($campaignObjectives));
        $campaignObjectivesArray->setIsAdditionalData(true);
        $campaignObjectivesArray->setSerializedName("campaign_objectives");
        $campaignObjectivesArray->setTitle("campaign_bjective");
        $campaignObjectivesArray->setState("campaign_bjective");
        $this->entityManager->persist($campaignObjectivesArray);

        $chapters[] = $campaignObjectivesArray;

        $channels = array_values($evaluationChannels);
        $evaluationChannels = new Chapter();
        $evaluationChannels->setContent(json_encode($channels));
        $evaluationChannels->setIsAdditionalData(true);
        $evaluationChannels->setSerializedName("channels");
        $evaluationChannels->setTitle("channels");
        $evaluationChannels->setState("channels");
        $this->entityManager->persist($evaluationChannels);

        $chapters[] = $evaluationChannels;

        $campaignObjectivesChapter = new Chapter();
        $campaignObjectivesChapter->setContent($campaignObjectivesChapterContent);
        $campaignObjectivesChapter->setIsAdditionalData(false);
        $campaignObjectivesChapter->setTitle("Campaign Objectives");
        $campaignObjectivesChapter->setState("visible");

        $this->entityManager->persist($campaignObjectivesChapter);
        $this->entityManager->flush();
        $this->entityManager->refresh($campaignObjectivesChapter);

        $campaignObjectivesChapter->setLocation(
            sprintf(
                '/api/evaluations/%s/chapters/%s',
                $evaluation->getCid(),
                $campaignObjectivesChapter->getId()
            )
        );
        $this->entityManager->persist($campaignObjectivesChapter);

        $chapters[] = $campaignObjectivesChapter;

        $evaluation->setChapters($chapters);

        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();

        return "evaluation generated!!!";
    }

    /**
     * @param string $cid
     *
     * @return Evaluation|null
     */
    protected function getExistingEvaluation($cid)
    {
        return $this->entityManager->getRepository('EvaluationEvaluationBundle:Evaluation')->findOneBy(['cid' => $cid]);
    }

    /**
     * @param string $name
     *
     * @return BusinessUnit|null
     */
    protected function getSupplier($name)
    {
        return $this->entityManager->getRepository('OroOrganizationBundle:BusinessUnit')
            ->getFirst();
    }

    protected function getTimingsFormatted($timings)
    {
        $timingPeriods = [];
        foreach ($timings as $timing) {
            switch ($timing['period']) {
                case 1:
                    $timingPeriods['Pre'] = $timing['period_date'];
                    break;
                case 2:
                    $timingPeriods['Pre'] = sprintf('%s-%s', $timingPeriods['Pre'], $timing['period_date']);
                    break;
                case 3:
                    $timingPeriods['During'] = $timing['period_date'];
                    break;
                case 4:
                    $timingPeriods['During'] = sprintf('%s-%s', $timingPeriods['During'], $timing['period_date']);
                    break;
                case 5:
                    $timingPeriods['Post'] = $timing['period_date'];
                    break;
                case 6:
                    $timingPeriods['Post'] = sprintf('%s-%s', $timingPeriods['Post'], $timing['period_date']);
                    break;
                default:
            }
        }

        return $timingPeriods;
    }

    protected function getStartDate($timings)
    {
        foreach ($timings as $timing) {
            if ($timing['period'] == 1) {
                return new \DateTime($timing['period_date']);
            }
        }

        return null;
    }

    protected function getEndDate($timings)
    {
        foreach ($timings as $timing) {
            if ($timing['period'] == 6) {
                return new \DateTime($timing['period_date']);
            }
        }

        return null;
    }
}
