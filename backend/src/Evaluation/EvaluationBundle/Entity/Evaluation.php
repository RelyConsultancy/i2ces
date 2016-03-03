<?php

namespace Evaluation\EvaluationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Evaluation\UtilBundle\Helpers\BusinessUnitHelper;
use JMS\Serializer\Annotation as JMS;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;

/**
 * Class Evaluation
 *
 * @ORM\Entity(repositoryClass="Evaluation\EvaluationBundle\Repository\EvaluationRepository")
 *
 * @package Evaluation\EvaluationBundle\Entity
 *
 * @Config(
 *   defaultValues={
 *     "ownership"={
 *         "owner_type"="BUSINESS_UNIT",
 *         "owner_field_name"="businessUnit",
 *         "owner_column_name"="business_unit_id"
 *     },
 *     "security"={
 *       "type"="ACL",
 *       "permissions"="VIEW;EDIT"
 *     },
 *     "entity"={
 *        "label"="Evaluation",
 *        "plural_label"="Evaluations",
 *        "description"="A generated evaluation"
 *     }
 *   }
 * )
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
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="guid", name="uid")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"list", "minimal"})
     * @JMS\SerializedName("id")
     * @JMS\Type("string")
     */
    protected $uid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="title")
     *
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("title")
     * @JMS\Type("string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="state")
     *
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("state")
     * @JMS\Type("string")
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="brand")
     *
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("brand")
     * @JMS\Type("string")
     */
    protected $brand;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="category")
     *
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("category")
     * @JMS\Type("string")
     */
    protected $category;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="start_date")
     *
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("start_date")
     * @JMS\Type("DateTime<'Y-m-d'>")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="end_date")
     *
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("end_date")
     * @JMS\Type("DateTime<'Y-m-d'>")
     */
    protected $endDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="generated_at")
     *
     * @JMS\Groups({"never_serialize"})
     * @JMS\SerializedName("generated_at")
     * @JMS\Type("DateTime<'Y-m-d\TH:i:s'>")
     */
    protected $generatedAt;

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
     * @JMS\Groups({"list"})
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
     * @JMS\Exclude()
     */
    protected $businessUnit;

    /**
     * @var Chapter[]
     *
     * @ORM\ManyToMany(targetEntity="Evaluation\EvaluationBundle\Entity\Chapter")
     * @ORM\JoinTable(
     *     name="evaluation_chapters",
     *     joinColumns={@ORM\JoinColumn(name="evaluation_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="chapter_id", referencedColumnName="id")}
     * )
     *
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("chapters")
     * @JMS\Type("array<'Evaluation\EvaluationBundle\Entity\Chapter'>")
     */
    protected $chapters;

    /**
     * @JMS\VirtualProperty()
     * @JMS\Groups({"list"})
     */
    public function getSupplier()
    {
        return BusinessUnitHelper::getBusinessUnitAsArray($this->getBusinessUnit());
    }

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
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
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
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
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
     * @return \DateTime
     */
    public function getGeneratedAt()
    {
        return $this->generatedAt;
    }

    /**
     * @param \DateTime $generatedAt
     */
    public function setGeneratedAt($generatedAt)
    {
        $this->generatedAt = $generatedAt;
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

    /**
     * @return Chapter[]
     */
    public function getChapters()
    {
        return $this->chapters;
    }

    /**
     * @param Chapter[] $chapters
     */
    public function setChapters($chapters)
    {
        $this->chapters = $chapters;
    }

    /**
     * @param BusinessUnit $owner
     */
    public function setOwner($owner)
    {
        if ($owner instanceof BusinessUnit) {
            $this->businessUnit = $owner;
        } else {
            throw new \InvalidArgumentException('Owner needs to be a supplier');
        }
    }

    /**
     * @return BusinessUnit
     */
    public function getOwner()
    {
        return $this->businessUnit;
    }

    /**
     * @param string $uid
     *
     * @return Chapter|null
     */
    public function getChapter($uid)
    {
        foreach ($this->chapters as $chapter) {
            if ($chapter->getUid() == $uid) {
                return $chapter;
            }
        }

        return null;
    }
}
