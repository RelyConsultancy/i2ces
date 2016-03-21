<?php

namespace i2c\SetupBundle\Command;

use Doctrine\DBAL\DBALException;
use i2c\SetupBundle\Services\SchemaUpdate;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DatabaseSchemaUpdateCommand
 *
 * @package i2c\SetupBundle\Command
 */
class DatabaseSchemaUpdateCommand extends ContainerAwareCommand
{
    /** @var SchemaUpdate */
    protected $schemaUpdateService;

    /** @var Logger */
    protected $logger;

    /**
     * DatabaseSchemaUpdateCommand constructor.
     *
     * @param SchemaUpdate $schemaUpdateService
     * @param Logger       $logger
     */
    public function __construct(SchemaUpdate $schemaUpdateService, Logger $logger)
    {
        parent::__construct(null);
        $this->schemaUpdateService = $schemaUpdateService;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('i2c:schema:update')
            ->addOption('version-number', null, InputOption::VALUE_REQUIRED, 'SQL Version used for generation.')
            ->addOption('filename', null, InputOption::VALUE_OPTIONAL, 'SQL file name.', 'migration.sql')
            ->setDescription('This command will update the database structure across all i2c entities');
    }

    /**
     * Update the schemas for a given version.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $version = $input->getOption('version-number');
            $fileName = $input->getOption('filename');
            $result = $this->schemaUpdateService->update($version, $fileName);
            $this->logger->addInfo(sprintf('Schemas were updated successfully for version \'%s\'!', $result));
        } catch (DBALException $ex) {
            $this->logger->addCritical($ex->getMessage());
        } catch (\Exception $ex) {
            $this->logger->addCritical($ex->getMessage());
        }

        return 0;
    }
}
