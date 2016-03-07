<?php

namespace Evaluation\EvaluationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Chapter
 *
 * @ORM\Entity(repositoryClass="Evaluation\EvaluationBundle\Repository\ChapterRepository")
 * @ORM\Table(name="chapter")
 *
 * @package Evaluation\EvaluationBundle\Entity
 */
class Chapter
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Exclude()
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="guid", name="uid")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"list", "full"})
     * @JMS\SerializedName("uid")
     * @JMS\Type("string")
     */
    protected $uid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="title")
     *
     * @JMS\Groups({"list", "full"})
     * @JMS\SerializedName("title")
     * @JMS\Type("string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="state")
     *
     * @JMS\Groups({"list", "full"})
     * @JMS\SerializedName("state")
     * @JMS\Type("string")
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="location")
     *
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("location")
     * @JMS\Type("string")
     */
    protected $location;

    /**
     * @var resource
     *
     * @ORM\Column(type="blob")
     *
     * @JMS\Accessor(getter="getContent", setter="setContent")
     * @JMS\Groups({"never_serialize"})
     * @JMS\Type("array")
     */
    protected $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @JMS\Groups({"never_serialize"})
     * @JMS\SerializedName("created_at")
     * @JMS\Type("DateTime<'Y-m-d\TH:i:s'>")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_at")
     *
     * @JMS\Groups({"never_serialize"})
     * @JMS\SerializedName("last_modified_at")
     * @JMS\Type("DateTime<'Y-m-d\TH:i:s'>")
     */
    protected $lastModifiedAt;

    /**
     * @JMS\VirtualProperty()
     * @JMS\Groups({"full"})
     * @JMS\SerializedName("content")
     * @JMS\Type("array")
     *
     * @return array
     */
    public function getContentAsArray()
    {
        $content = stream_get_contents($this->content);

        return json_decode($content, true);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

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
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return resource
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getLastModifiedAt()
    {
        return $this->lastModifiedAt;
    }

    /**
     * @param mixed $lastModifiedAt
     */
    public function setLastModifiedAt($lastModifiedAt)
    {
        $this->lastModifiedAt = $lastModifiedAt;
    }
}
