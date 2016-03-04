<?php

namespace Evaluation\SetupBundle\Command;

use Doctrine\ORM\EntityManager;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
use Oro\Bundle\UserBundle\Entity\Role;
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

    protected $initialSetupConfig;

    public function __construct($initialSetupConfig)
    {
        $this->initialSetupConfig = $initialSetupConfig;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('i2c:initial-setup')
            ->setDescription('This command will update the database structure to work with the i2c application.
            Should be ran once when the server was just installed');
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

        $this->createDummySuppliers();

        $this->createRoles();


    }

    /**
     * Creates dummy supplier based on the configuration
     */
    protected function createDummySuppliers()
    {
        if ($this->entityManager->getRepository('OroOrganizationBundle:BusinessUnit')->getBusinessUnitsCount() > 1) {
            // this means that the tables are already populated
            return;
        }

        // at this point there is only the 'Main' business unit that is generated during the oro setup
        $i2cBusinessUnit = $this->entityManager->getRepository('OroOrganizationBundle:BusinessUnit')->getFirst();

        /** @var array $supplierConfig */
        foreach ($this->initialSetupConfig['suppliers'] as $supplierConfig) {
            $businessUnitOne = new BusinessUnit();
            $businessUnitOne->setName($supplierConfig['name']);
            $businessUnitOne->setEmail($supplierConfig['email']);
            $businessUnitOne->setOwner($i2cBusinessUnit);
            $businessUnitOne->setFax($supplierConfig['fax']);
            $businessUnitOne->setPhone($supplierConfig['phone']);
            $businessUnitOne->setWebsite($supplierConfig['website']);
            $businessUnitOne->setOrganization($i2cBusinessUnit->getOrganization());
            $this->entityManager->persist($businessUnitOne);
        }

        $this->entityManager->flush();
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
            $existingRole = $em->getRepository('OroUserBundle:Role')->findOneBy(['label' => $roleConfig['name']]);
            if (is_null($existingRole)) {
                $role = new Role();
                $role->setLabel($roleConfig['name']);
                $role->setRole($roleConfig['identifier']);

                $em->persist($role);
                $em->flush($role);


                $oid = $aclManager->getOid($roleConfig['access']['oid']);

                $sid = $aclManager->getSid($role);

                $builder = $aclManager->getMaskBuilder($oid);

                foreach ($roleConfig['access']['permissions'] as $permission) {
                    $builder = $builder->add($permission);
                }

                $mask = $builder->get();

                $aclManager->setPermission($sid, $oid, $mask);

                $aclManager->flush();
            }
        }
    }
}
