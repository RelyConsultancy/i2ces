<?php

namespace Evaluation\SetupBundle\Command;

use Evaluation\SetupBundle\Services\AbstractSchemaUpdateService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TableSchemaUpdateCommand
 *
 * @package Evaluation\SetupBundle\Command
 */
class TableSchemaUpdateCommand extends ContainerAwareCommand
{
    /** @var  AbstractSchemaUpdateService */
    protected $schemaUpdateService;

    protected $commandName;

    /**
     * EvaluationSchemaUpdateCommand constructor.
     *
     * @param AbstractSchemaUpdateService $schemaUpdateService
     * @param null                        $commandName
     */
    public function __construct(AbstractSchemaUpdateService $schemaUpdateService, $commandName)
    {
        $this->schemaUpdateService = $schemaUpdateService;
        $this->commandName = $commandName;

        parent::__construct($commandName);
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription(
                sprintf(
                    'This command will update the database structure for the table: %s',
                    $this->schemaUpdateService->getTableName()
                )
            );
    }

    /**
     * Update the schmas for all the tables defined in the schema update container
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->schemaUpdateService->updateSchema();
            $output->writeln(
                sprintf(
                    '%s schema was updated successfully!',
                    $this->schemaUpdateService->getTableName()
                )
            );
        } catch (\Exception $ex) {
            $output->writeln('There was an error while updating the schema');

            return -1;
        }

        return 0;
    }
}
