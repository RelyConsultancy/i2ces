<?php

namespace i2c\EvaluationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;

/**
 * Class Evaluation
 *
 * @ORM\Entity(repositoryClass="i2c\EvaluationBundle\Repository\EvaluationRepository")
 * @ORM\Table(name="i2c_evaluation")
 *
 * @package i2c\EvaluationBundle\Entity
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
    const STATE_GENERATING = 'generating';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("id")
     * @JMS\Type("string")
     */
    protected $id;

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
     * @ORM\Column(type="string", name="version_number")
     *
     * @JMS\Groups({"never_serialize"})
     * @JMS\SerializedName("version_number")
     * @JMS\Type("string")
     */
    protected $versionNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="latest_pdf_path")
     *
     * @JMS\Groups({"never_serialize"})
     * @JMS\SerializedName("latest_pdf_path")
     * @JMS\Type("string")
     */
    protected $latestPdfPath;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="temporary_pdf_path")
     *
     * @JMS\Groups({"never_serialize"})
     * @JMS\SerializedName("temporary_pdf_path")
     * @JMS\Type("string")
     */
    protected $temporaryPdfPath;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="pdf_markers")
     *
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("pdf_markers")
     * @JMS\Type("string")
     */
    protected $pdfMarkers;

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
     * @JMS\SerializedName("date_start")
     * @JMS\Type("DateTime<'Y-m-d'>")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="end_date")
     *
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("date_end")
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
     * @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    protected $generatedAt;

    /**
     * @var BusinessUnit
     *
     * @ORM\OneToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\BusinessUnit")
     * @ORM\JoinColumn(name="business_unit_id", referencedColumnName="id")
     *
     * @JMS\Groups({"never_serialize"})
     * @JMS\SerializedName("businessUnit")
     * @JMS\Type("Oro\Bundle\OrganizationBundle\Entity\BusinessUnit")
     */
    protected $businessUnit;

    /**
     * @var Chapter[]
     *
     * @ORM\ManyToMany(targetEntity="i2c\EvaluationBundle\Entity\Chapter")
     * @ORM\JoinTable(
     *     name="i2c_evaluation_chapters",
     *     joinColumns={@ORM\JoinColumn(name="evaluation_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="chapter_id", referencedColumnName="id")}
     * )
     *
     * @JMS\Accessor(getter="getChapters")
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("chapters")
     * @JMS\Type("array<'i2c\EvaluationBundle\Entity\Chapter'>")
     */
    protected $chapters;

    /**
     * @JMS\VirtualProperty()
     * @JMS\Groups({"list"})
     * @JMS\SerializedName("display_title")
     *
     * @return array
     */
    public function getDisplayName()
    {
        return sprintf(
            '%s: %s',
            $this->brand,
            $this->title
        );
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\Groups({"list"})
     *
     * @return array
     */
    public function getSupplier()
    {
        return [
            'id' => $this->businessUnit->getId(),
            'name' => $this->businessUnit->getName(),
            'email' => $this->businessUnit->getEmail(),
            'phone' => $this->businessUnit->getPhone(),
            'website' => $this->businessUnit->getWebsite(),
        ];
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\Groups({"list"})
     *
     * @return array
     */
    public function gethasPdf()
    {
        if (is_null($this->getLatestPdfPath())) {
            return false;
        }

        return true;
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
     * @return string
     */
    public function getVersionNumber()
    {
        return $this->versionNumber;
    }

    /**
     * @param string $versionNumber
     */
    public function setVersionNumber($versionNumber)
    {
        $this->versionNumber = $versionNumber;
    }

    /**
     * @return string
     */
    public function getLatestPdfPath()
    {
        return $this->latestPdfPath;
    }

    /**
     * @param string $latestPdfPath
     */
    public function setLatestPdfPath($latestPdfPath)
    {
        $this->latestPdfPath = $latestPdfPath;
    }

    /**
     * @return string
     */
    public function getTemporaryPdfPath()
    {
        return $this->temporaryPdfPath;
    }

    /**
     * @param string $temporaryPdfPath
     */
    public function setTemporaryPdfPath($temporaryPdfPath)
    {
        $this->temporaryPdfPath = $temporaryPdfPath;
    }

    /**
     * @return string
     */
    public function getPdfMarkers()
    {
        return $this->pdfMarkers;
    }

    /**
     * @param string $pdfMarkers
     */
    public function setPdfMarkers($pdfMarkers)
    {
        $this->pdfMarkers = $pdfMarkers;
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
     * @return Chapter[]
     */
    public function getChaptersOrderedByOrder()
    {
        $result = array();
        foreach ($this->chapters as $chapter) {
            if (!$chapter->getIsAdditionalData()) {
                $result[$chapter->getOrder()] = $chapter;
            }
        }

        ksort($result);
        return $result;
    }

    /**
     * @return Chapter[]
     */
    public function getChaptersIncludingAdditionalData()
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
        $this->latestPdfPath = null;
    }

    /**
     * Marks an evaluation as unpublished
     */
    public function markAsDraft()
    {
        $this->state = self::STATE_DRAFT;
    }

    /**
     * Marks an evaluation as unpublished
     */
    public function markAsGenerating()
    {
        $this->state = self::STATE_GENERATING;
    }

    /**
     * Marks an evaluation as unpublished
     */
    public function markAsPublished()
    {
        $this->state = self::STATE_PUBLISHED;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        if (self::STATE_PUBLISHED != $this->state) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isDraft()
    {
        if (self::STATE_DRAFT != $this->state) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isGenerating()
    {
        if (self::STATE_GENERATING != $this->state) {
            return false;
        }

        return true;
    }
}
