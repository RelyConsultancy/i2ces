<?php

namespace i2c\PageBundle\Services;

use Doctrine\ORM\EntityManager;
use i2c\PageBundle\Entity\Page as PageEntity;
use JMS\Serializer\Serializer;

/**
 * Class Page
 *
 * @package i2c\PageBundle\Services
 */
class Page
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var Serializer */
    protected $serializer;

    /**
     * Page constructor.
     *
     * @param Serializer    $serializer
     * @param EntityManager $entityManager
     */
    public function __construct(Serializer $serializer, EntityManager $entityManager)
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * @param PageEntity $page
     * @param string     $updatePage
     *
     * @return PageEntity
     */
    public function updatePage(PageEntity $page, $updatePage)
    {
        $updatePage = $this->serializer->deserialize(
            $updatePage,
            'i2c\PageBundle\Entity\Page',
            'json'
        );

        $page->setContent($updatePage->getContent());

        $page->setTitle($updatePage->getTitle());

        $this->entityManager->persist($page);

        $this->entityManager->flush();

        $this->entityManager->refresh($page);

        return $page;
    }
}
