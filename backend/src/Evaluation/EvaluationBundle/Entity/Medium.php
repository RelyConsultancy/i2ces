<?php

namespace Evaluation\EvaluationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Medium
 *
 * @ORM\Entity(repositoryClass="Evaluation\EvaluationBundle\Repository\MediumRepository")
 *
 * @package Evaluation\EvaluationBundle\Entity
 */
class Medium
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", name="id")
     *
     * @JMS\Exclude()
     * @JMS\Groups({"_id"})
     * @JMS\SerializedName("id")
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="guid", name="uid")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"general"})
     * @JMS\SerializedName("uid")
     * @JMS\Type("string")
     */
    protected $uid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="medium_name")
     *
     * @JMS\Groups({"general"})
     * @JMS\SerializedName("medium_name")
     * @JMS\Type("string")
     */
    protected $mediumName;

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
    public function getMediumName()
    {
        return $this->mediumName;
    }

    /**
     * @param string $mediumName
     */
    public function setMediumName($mediumName)
    {
        $this->mediumName = $mediumName;
    }
}
