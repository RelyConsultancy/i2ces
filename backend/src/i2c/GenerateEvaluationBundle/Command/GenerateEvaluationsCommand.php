<?php

namespace i2c\GenerateEvaluationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Form\Extension\Templating\TemplatingRendererEngine;

/**
 * Class GenerateEvaluationsCommand
 *
 * @package i2c\EvaluationBundle\Command
 */
class GenerateEvaluationsCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('i2c:evaluation:generate')
            ->setDescription('This command will import the i2c data from a csv to a table');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $templateRenderer = $this->getContainer()->get('templating');

        $mediaLaydownTableContent = [
            [
                'media' => 'paper',
                'start_date' => '2015-7-01',
                'end_date' => '2015-7-08',
            ],
            [
                'media' => 'paper',
                'start_date' => '2015-06-24',
                'end_date' => '2015-07-08',
            ],
        ];

        $mediaLaydownHeaders = [];

        $formattedHeaders = [];
        $invertedHeader = [];

        foreach ($mediaLaydownTableContent as $media) {
            if (!isset($media['start_date'])) {
                $formattedHeaders[] = $media['start_date'];
                $invertedHeader[$media['start_date']] = true;
            }
            if (!isset($media['end_date'])) {
                $formattedHeaders[] = $media['end_date'];
                $invertedHeader[$media['end_date']] = true;
            }
        }
        $mediaLaydownColors = ["#ccc", "#fff", "#000"];
        $inversedMediaLaydownHeaders = array_flip($mediaLaydownHeaders);
        $mediaLaydownTableData = [
            [
                'mediaName' => "somepaper",
                'rowData' => $inversedMediaLaydownHeaders
            ]
        ];

        $json = $templateRenderer->render(
            'EvaluationEvaluationBundle:CampaignBackground:campaign-background-version-1.json.twig',
            [
                "campaignObjectives"  => ["nop","yep","wat?"],
                "timings"  => ["nop","yep","wat?"],
                "evaluatedChannels"  => ["nop","yep","wat?"],
                "evaluatedCost"  => ["nop","yep","wat?"],
                "mediaLaydownHeaders"  => $mediaLaydownHeaders,
                "mediaLaydownTableData" =>$mediaLaydownTableData,
                "mediaLaydownColors" => $mediaLaydownColors,
            ]
        );

        $camapignObjectiveDataService = $this->getContainer()->get('i2c_generate_evaluation.campaign_objective_data');

        $output->writeln($camapignObjectiveDataService->getMediaLaydownHeaderPeriods("i2c1510047a"));
    }
}
