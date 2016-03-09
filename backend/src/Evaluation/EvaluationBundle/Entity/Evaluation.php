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
 * @ORM\Table(name="evaluation")
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
    const STATE_DRAFT = 'draft';
    const STATE_PUBLISHED = 'published';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"list", "minimal"})
     * @JMS\SerializedName("id")
     * @JMS\Type("string")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="guid", name="uid")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"list", "minimal"})
     * @JMS\SerializedName("uid")
     * @JMS\Type("string")
     */
    protected $uid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="cid")
     *
     * @JMS\Groups({"list", "minimal"})
     * @JMS\SerializedName("cid")
     * @JMS\Type("string")
     */
    protected $cid;

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
     * @JMS\Accessor(getter="getChapters")
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("chapters")
     * @JMS\Type("array<'Evaluation\EvaluationBundle\Entity\Chapter'>")
     */
    protected $chapters;

    /**
     * @JMS\VirtualProperty()
     * @JMS\Groups({"list"})
     *
     * @return array
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
     * @return array
     */
    public function getCustomEntities()
    {
        $result = array();
        foreach ($this->chapters as $chapter) {
            if ($chapter->getIsAdditionalData()) {
                $result[$chapter->getSerializedName()] = $chapter->getContentAsArray();
            }
        }

        return $result;
    }

    /**
     * @return Chapter[]
     */
    public function getChapters()
    {
        $result = array();
        foreach ($this->chapters as $chapter) {
            if (!$chapter->getIsAdditionalData()) {
                $result[] = $chapter;
            }
        }

        return $result;
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
     * @param string $id
     *
     * @return Chapter|null
     */
    public function getChapter($id)
    {
        /** @var Chapter $chapter */
        foreach ($this->chapters as $chapter) {
            if ($chapter->getId() == $id) {
                return $chapter;
            }
        }

        return null;
    }

    /**
     * Marks an evaluation as published
     */
    public function publish()
    {
        $this->state = self::STATE_PUBLISHED;
    }

    /**
     * Marks an evaluation as unpublished
     */
    public function unpublish()
    {
        $this->state = self::STATE_DRAFT;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        if ($this->state == self::STATE_PUBLISHED) {
            return true;
        }

        return false;
    }
}
