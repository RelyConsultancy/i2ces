<?php

namespace i2c\SetupBundle\Command;

use Doctrine\ORM\EntityManager;
use Oro\Bundle\UserBundle\Entity\Role;
use Oro\Bundle\UserBundle\Entity\UserManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InitialSetupCommand
 *
 * @package Evaluation\SetupBundle\Command
 */
class InitialSetupCommand extends ContainerAwareCommand
{
    /** @var  EntityManager */
    protected $entityManager;

    /** @var array */
    protected $initialSetupConfig;

    /** @var UserManager */
    protected $userManager;

    /**
     * InitialSetupCommand constructor.
     *
     * @param null|string $initialSetupConfig
     * @param UserManager $userManager
     */
    public function __construct($initialSetupConfig, UserManager $userManager)
    {
        $this->initialSetupConfig = $initialSetupConfig;
        $this->userManager = $userManager;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('i2c:initial-setup')
            ->setDescription(
                'This command will update the database structure to work with the i2c application.
It will only update the initial roles in the application once.'
            );
    }

    /**
     * Update the schemas for all the tables defined in the schema update container
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $this->entityManager = $container->get('doctrine')->getEntityManager();

        $this->createRoles();

        $logger = $container->get('logger');
        $logger->addInfo("Initial setup completed successfully");
        $output->writeln("Initial setup completed successfully");
    }

    /**
     * Creates roles based on the configuration
     *
     * @throws \Exception
     */
    public function createRoles()
    {
        $aclManager = $this->getContainer()->get('oro_security.acl.manager');
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        foreach ($this->initialSetupConfig['roles'] as $roleConfig) {
            $existingRole = $em->getRepository('OroUserBundle:Role')->findOneBy(
                [
                    'label' => $roleConfig['new_name'],
                ]
            );

            if (!is_null($existingRole)) {
                continue;
            }

            $role = $em->getRepository('OroUserBundle:Role')->findOneBy(
                [
                    'label' => $roleConfig['name'],
                ]
            );

            $role->setLabel($roleConfig['new_name']);

            $em->persist($role);

            foreach ($roleConfig['access'] as $accessConfig) {
                $oid = $aclManager->getOid($accessConfig['oid']);

                $sid = $aclManager->getSid($role);

                $builder = $aclManager->getMaskBuilder($oid);

                foreach ($accessConfig['permissions'] as $permission) {
                    $builder = $builder->add($permission);
                }

                $mask = $builder->get();

                $aclManager->setPermission($sid, $oid, $mask);
            }

            $aclManager->flush();

        }
        $em->flush();
    }
}
