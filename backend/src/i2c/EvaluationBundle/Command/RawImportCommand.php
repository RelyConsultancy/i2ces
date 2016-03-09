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

        parent::__construct("i2c:data-import");
    }
    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('i2c:data-import')
            ->addOption(
                'import-folder-path',
                null,
                InputOption::VALUE_REQUIRED,
                'The absolute path of the directory containing the csv files'
            )
            ->addOption(
                'field-separator',
                null,
                InputOption::VALUE_OPTIONAL,
                'The character(s) that separate the fields in the csv file',
                ','
            )
            ->addOption(
                'line-endings',
                null,
                InputOption::VALUE_OPTIONAL,
                'The characters that separate lines in the csv file, defaults to "\n"',
                '\n'
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
        $doctrine = $this->getContainer()->get('doctrine');

        $entityManager = $doctrine->getEntityManager();

        $dbhost = $this->getContainer()->getParameter('database_host');
        $dbuser = $this->getContainer()->getParameter('database_user');
        $dbpass = $this->getContainer()->getParameter('database_password');
        $connParams = $entityManager->getConnection()->getParams();

        $pdoConn = new \PDO('mysql:host=' . $dbhost . ';dbname=' . $connParams['dbname'], $dbuser, $dbpass, array(
            \PDO::MYSQL_ATTR_LOCAL_INFILE => true
        ));

        foreach ($this->rawTablesConfig as $key => $value) {
            $this->createTable($key, $value, $pdoConn);
            $output->writeln(
                sprintf(
                    'Table "%s" was created successfully',
                    $key
                )
            );
        }

        $importFolderPath = $input->getOption('import-folder-path');

        $lineEndings = $input->getOption('line-endings');
        $fieldSeparator = $input->getOption('field-separator');

        foreach ($this->rawTablesConfig as $key => $value) {
            $rows = $this->loadDataFromFile(
                $key,
                array_keys($value['columns']),
                sprintf('%s/%s', $importFolderPath, $value['file_name']),
                $fieldSeparator,
                $lineEndings,
                $pdoConn
            );

            $output->writeln(sprintf('Imported "%s" lines into table %s', $rows, $key));
        }
    }


    /**
     * @param string $tableName
     * @param string $filePath
     * @param array  $columns
     * @param string $fieldSeparator
     * @param string $lineEndings
     * @param \PDO   $connection
     *
     * @return int
     */
    protected function loadDataFromFile($tableName, $columns, $filePath, $fieldSeparator, $lineEndings, $connection)
    {
        $query = sprintf(
            "LOAD DATA LOCAL INFILE '%s' INTO TABLE `%s`
            FIELDS TERMINATED BY '%s'
            ENCLOSED BY '\"'
            LINES TERMINATED BY '%s'
            IGNORE 1 lines
            (%s)
            ",
            $filePath,
            $tableName,
            $fieldSeparator,
            $lineEndings,
            implode(',', $columns)
        );

        return $connection->exec($query);
    }

    /**
     * @param string     $tableName
     * @param array      $config
     * @param \PDO       $connection
     */
    protected function createTable($tableName, $config, $connection)
    {
        $query = sprintf(
            'CREATE TABLE IF NOT EXISTS `%s` (`id` INT(11) NOT NULL AUTO_INCREMENT, ',
            $tableName
        );

        $tableColumns = [];
        foreach ($config['columns'] as $fieldName => $fieldConfig) {
            $tableColumns[] = sprintf(
                '`%s` %s',
                $fieldName,
                $fieldConfig
            );
        }
        $query = sprintf(
            '%s %s, PRIMARY KEY(`id`))',
            $query,
            implode(',', $tableColumns)
        );

        $connection->exec($query);
    }
}
