<?php

namespace i2c\EvaluationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class TableData
 *
 * @ORM\Entity()
 * @ORM\Table(name="i2c_table_data")
 *
 * @package i2c\EvaluationBundle\Entity
 */
class TableData
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"never_serialize"})
     * @JMS\SerializedName("id")
     * @JMS\Type("string")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="cid")
     *
     * @JMS\Groups({"never_serialize"})
     * @JMS\SerializedName("cid")
     * @JMS\Type("string")
     */
    protected $cid;

    /**
     * @var resource
     *
     * @ORM\Column(type="blob", name="content")
     *
     * @JMS\Groups({"never_serialize"})
     * @JMS\SerializedName("content")
     * @JMS\Type("array")
     */
    protected $content;

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
    public function getCid()
    {
        return $this->cid;
    }

    /**
     * @param string $cid
     */
    public function setCid($cid)
    {
        $this->cid = $cid;
    }

    /**
     * @return resource
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return array
     */
    public function getContentAsArray()
    {
        $content = stream_get_contents($this->content);

        return json_decode($content, true);
    }

    /**
     * @param resource $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}
