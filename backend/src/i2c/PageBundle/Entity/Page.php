<?php

namespace i2c\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;

/**
 * Class Page
 *
 * @ORM\Entity(repositoryClass="i2c\PageBundle\Repository\PageRepository")
 * @ORM\Table(name="i2c_pages")
 *
 * @package i2c\PageBundle\Entity
 *
 * @Config(
 *   defaultValues={
 *     "security"={
 *       "type"="ACL",
 *       "permissions"="VIEW;EDIT"
 *     },
 *     "entity"={
 *        "label"="Page",
 *        "plural_label"="Pages",
 *        "description"=""
 *     }
 *   }
 * )
 */
class Page
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="string", length=255, name="type")
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @JMS\Groups({"never_serialize"})
     * @JMS\SerializedName("type")
     * @JMS\Type("string")
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="title")
     *
     * @JMS\Groups({"full"})
     * @JMS\SerializedName("title")
     * @JMS\Type("string")
     */
    protected $title;

    /**
     * @var resource
     *
     * @ORM\Column(type="blob")
     *
     * @JMS\Accessor(getter="getContentAsString", setter="setContent")
     * @JMS\Groups({"full"})
     * @JMS\SerializedName("content")
     * @JMS\Type("string")
     */
    protected $content;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return resource
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param resource $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getContentAsArray()
    {
        return array($this->getContentAsString());
    }

    /**
     * @return string
     */
    public function getContentAsString()
    {
        $content = stream_get_contents($this->content);

        return $content;
    }
}
