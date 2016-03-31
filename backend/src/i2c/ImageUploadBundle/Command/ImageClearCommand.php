<?php

namespace i2c\ImageUploadBundle\Command;

use i2c\ImageUploadBundle\Services\RemoveImages;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImageClearCommand
 *
 * @package i2c\ImageUploadBundle\Command
 */
class ImageClearCommand extends ContainerAwareCommand
{
    /** @var RemoveImages */
    protected $removeImagesService;

    /** @var Logger */
    protected $logger;

    /**
     * ImageClearCommand constructor.
     *
     * @param RemoveImages $removeImagesService
     */
    public function __construct(RemoveImages $removeImagesService, Logger $logger)
    {
        parent::__construct(null);
        $this->removeImagesService = $removeImagesService;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('i2c:image:clear')
            ->setDescription('This command will remove the extra images in the system.');
    }

    /**
     * Remove extra images from the system.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     * @throws \LogicException|\RuntimeException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $result = $this->removeImagesService->removeExtraImages();

            $successMessage = sprintf('(%s) images were removed from the system', $result);
            $this->logger->addInfo($successMessage);
            $output->writeln($successMessage);
        } catch (\Exception $ex) {
            $this->logger->addCritical($ex->getTraceAsString());
            throw new \LogicException($ex->getMessage());
        }
    }
}
