<?php

namespace Evaluation\EvaluationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Medium
 *
 * @ORM\Entity(repositoryClass="Evaluation\EvaluationBundle\Repository\MediumRepository")
 * @ORM\Table(name="medium")
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
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="label")
     *
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("label")
     * @JMS\Type("string")
     */
    protected $label;

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
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
}
