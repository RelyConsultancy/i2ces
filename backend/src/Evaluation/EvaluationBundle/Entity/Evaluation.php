<?php

namespace Evaluation\EvaluationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;

/**
 * Class Evaluation
 *
 * @ORM\Entity(repositoryClass="Evaluation\EvaluationBundle\Repository\EvaluationRepository")
 *
 * @package Evaluation\EvaluationBundle\Entity
 */
class Evaluation
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Exclude()
     * @JMS\Groups({"general"})
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
     * @ORM\Column(type="string", length=255, name="display_name")
     *
     * @JMS\Groups({"general"})
     * @JMS\SerializedName("display_name")
     * @JMS\Type("string")
     */
    protected $displayName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="start_date")
     *
     * @JMS\Groups({"general"})
     * @JMS\SerializedName("start_date")
     * @JMS\Type("DateTime<'Y-m-d\TH:i:s'>")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="end_date")
     *
     * @JMS\Groups({"general"})
     * @JMS\SerializedName("end_date")
     * @JMS\Type("DateTime<'Y-m-d\TH:i:s'>")
     */
    protected $endDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="evaluation_generation_date")
     *
     * @JMS\Groups({"general"})
     * @JMS\SerializedName("evaluation_generation_date")
     * @JMS\Type("DateTime<'Y-m-d\TH:i:s'>")
     */
    protected $evaluationGenerationDate;

    /**
     * @var Medium[]
     *
     * @ORM\ManyToMany(targetEntity="Evaluation\EvaluationBundle\Entity\Medium")
     * @ORM\JoinTable(
     *     name="evaluation_mediums",
     *     joinColumns={@ORM\JoinColumn(name="evaluation_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="medium_id", referencedColumnName="id")}
     * )
     *
     * @JMS\Groups({"general"})
     * @JMS\SerializedName("mediums")
     * @JMS\Type("array<'Evaluation\EvaluationBundle\Entity\Medium'>")
     */
    protected $mediums;

    /**
     * @var BusinessUnit
     *
     * @ORM\OneToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\BusinessUnit")
     * @ORM\JoinColumn(name="business_unit_id", referencedColumnName="id")
     *
     * @JMS\Groups({"general"})
     * @JMS\MaxDepth(1)
     * @JMS\SerializedName("business_unit")
     * @JMS\Type("Oro\Bundle\OrganizationBundle\Entity\BusinessUnit")
     */
    protected $businessUnit;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param mixed $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getEvaluationGenerationDate()
    {
        return $this->evaluationGenerationDate;
    }

    /**
     * @param mixed $evaluationGenerationDate
     */
    public function setEvaluationGenerationDate($evaluationGenerationDate)
    {
        $this->evaluationGenerationDate = $evaluationGenerationDate;
    }

    /**
     * @return mixed
     */
    public function getMediums()
    {
        return $this->mediums;
    }

    /**
     * @param mixed $mediums
     */
    public function setMediums($mediums)
    {
        $this->mediums = $mediums;
    }

    /**
     * @return BusinessUnit
     */
    public function getBusinessUnit()
    {
        return $this->businessUnit;
    }

    /**
     * @param BusinessUnit $businessUnit
     */
    public function setBusinessUnit($businessUnit)
    {
        $this->businessUnit = $businessUnit;
    }
}
