<?php

namespace i2c\SetupBundle\Command;

use Doctrine\ORM\EntityManager;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
use Oro\Bundle\UserBundle\Entity\Role;
use Oro\Bundle\UserBundle\Entity\User;
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

        // for now we create the roles and the users manually
//        $this->createRoles();

        $this->createDummySuppliers();

        $output->writeln("Initial setup completed successfully");
    }

    /**
     * Creates dummy supplier based on the configuration
     */
    protected function createDummySuppliers()
    {
        if ($this->entityManager->getRepository('OroOrganizationBundle:BusinessUnit')->getBusinessUnitsCount() > 1) {
            // this means that the tables are already populated
//            return;
        }

        // at this point there is only the 'Main' business unit that is generated during the oro setup
        $i2cBusinessUnit = $this->entityManager->getRepository('OroOrganizationBundle:BusinessUnit')->getFirst();

        /** @var array $supplierConfig */
        foreach ($this->initialSetupConfig['suppliers'] as $supplierConfig) {
            $dummyBusinessUnit = new BusinessUnit();
            $dummyBusinessUnit->setName($supplierConfig['name']);
            $dummyBusinessUnit->setEmail($supplierConfig['email']);
            $dummyBusinessUnit->setOwner($i2cBusinessUnit);
            $dummyBusinessUnit->setFax($supplierConfig['fax']);
            $dummyBusinessUnit->setPhone($supplierConfig['phone']);
            $dummyBusinessUnit->setWebsite($supplierConfig['website']);
            $dummyBusinessUnit->setOrganization($i2cBusinessUnit->getOrganization());

            // for now the user creation doesn't work properly
            // todo update this
//            $userForBusinessUnit = $this->createUser($supplierConfig['user']);
//
//            $userForBusinessUnit->setOrganization($i2cBusinessUnit->getOrganization());
//
//            $userForBusinessUnit->addBusinessUnit($dummyBusinessUnit);
//
//            $dummyBusinessUnit->addUser($userForBusinessUnit);
//            $this->entityManager->persist($userForBusinessUnit);

            $this->entityManager->persist($dummyBusinessUnit);
        }

        $this->entityManager->flush();
    }

    /**
     * @param array        $userConfig
     *
     * @return User
     */
    public function createUser($userConfig)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository('OroUserBundle:User')
            ->findOneBy(['username' => $userConfig['username']]);
        if (is_null($user)) {
            $user = $this->userManager->createUser();
            $user->setEnabled(true);
        }

        $role = $this->entityManager->getRepository('OroUserBundle:Role')->findOneBy(['label' => $userConfig['role']]);

        if (!is_null($role)) {
            $user->addRole($role);
        }

        $user->setFirstName($userConfig['first_name']);
        $user->setLastName($userConfig['last_name']);
        $user->setMiddleName($userConfig['middle_name']);
        $user->setUsername($userConfig['username']);
        $user->setPlainPassword($userConfig['password']);
        $user->setEmail($userConfig['email']);
        $this->userManager->updatePassword($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->entityManager->refresh($user);

        return $user;
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
