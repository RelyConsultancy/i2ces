<?php

namespace i2c\SetupBundle\Command;

use Doctrine\DBAL\DBALException;
use i2c\SetupBundle\Services\SchemaUpdate;
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

    /**
     * DatabaseSchemaUpdateCommand constructor.
     *
     * @param SchemaUpdate $schemaUpdateService
     */
    public function __construct(SchemaUpdate $schemaUpdateService)
    {
        parent::__construct(null);
        $this->schemaUpdateService = $schemaUpdateService;
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
            $output->writeln(
                sprintf('Schemas were updated successfully for version \'%s\'!', $result)
            );
        } catch (DBALException $ex) {
            $output->writeln($ex->getMessage());
        } catch (\Exception $ex) {
            $output->writeln($ex->getMessage());
        }

        return 0;
    }
}
