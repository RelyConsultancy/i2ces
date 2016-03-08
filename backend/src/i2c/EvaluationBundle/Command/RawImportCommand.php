<?php

namespace i2c\EvaluationBundle\Command;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RawImportCommand
 *
 * @package i2c\EvaluationBundle\Command
 */
class RawImportCommand extends ContainerAwareCommand
{

    /** @var array */
    protected $rawTablesConfig;

    /**
     * RawImportCommand constructor.
     *
     * @param array $rawTablesConfig
     */
    public function __construct($rawTablesConfig)
    {
        $this->rawTablesConfig = $rawTablesConfig;

        parent::__construct();
    }
    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName("i2c:data-import")
            ->addOption(
                "import-folder-path",
                null,
                InputOption::VALUE_REQUIRED,
                "The absolute path of the directory containing the csv files"
            )
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
        $this->createTables();
    }

    protected function createTables()
    {
        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();

        foreach ($this->rawTablesConfig as $key => $value) {
            $connection = $entityManager->getConnection();
            $this->createTable($key, $value, $connection);
        }
    }

    /**
     * @param string     $tableName
     * @param array      $config
     * @param Connection $connection
     */
    protected function createTable($tableName, $config, $connection)
    {
        $query = sprintf(
            'CREATE TABLE IF NOT EXISTS `%s` (',
            $tableName
        );

        foreach ($config['columns'] as $fieldName => $fieldConfig) {
            $query = sprintf(
                '%s %s %s',
                $query,
                $fieldName,
                $fieldConfig
            );
        }
        $query = sprintf(
            '`%s` PRIMARY KEY(%s))',
            $query,
            $config['primary_key']
        );

        $connection->exec($query);
    }
}
