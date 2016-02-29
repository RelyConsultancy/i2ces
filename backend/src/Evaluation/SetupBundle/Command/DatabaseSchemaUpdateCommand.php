<?php

namespace Evaluation\SetupBundle\Command;

use Evaluation\SetupBundle\Services\AbstractSchemaUpdateService;
use Evaluation\SetupBundle\Services\SchemaUpdateContainer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DatabaseSchemaUpdateCommand
 *
 * @package Evaluation\SetupBundle\Command
 */
class DatabaseSchemaUpdateCommand extends ContainerAwareCommand
{
    /** @var SchemaUpdateContainer  */
    protected $schemaUpdateContainer;

    /**
     * DatabaseSchemaUpdateCommand constructor.
     *
     * @param SchemaUpdateContainer $schemaUpdateContainer
     * @param string|null           $commandName
     */
    public function __construct(SchemaUpdateContainer $schemaUpdateContainer, $commandName = null)
    {
        parent::__construct($commandName);
        $this->schemaUpdateContainer = $schemaUpdateContainer;
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName("i2c:schema:update")
            ->setDescription("This command will update the database structure across all i2c entities");
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
        $schemaUpdateServices = $this->schemaUpdateContainer->getSchemaUpdateServices();

        $currentProcessedTable = null;
        try {
            /** @var AbstractSchemaUpdateService $schemaUpdateService */
            foreach ($schemaUpdateServices as $schemaUpdateService) {
                $currentProcessedTable = $schemaUpdateService->getTableName();
                $schemaUpdateService->updateSchema();
                $output->writeln("Updated schema for table: ".$schemaUpdateService->getTableName());
            }
            $output->writeln("Schemas were updated successfully!");
        } catch (\Exception $ex) {
            $output->writeln("There was an error while updating the schema for the table: ".$currentProcessedTable);
            $output->writeln($ex->getMessage());
        }

        return 0;
    }
}