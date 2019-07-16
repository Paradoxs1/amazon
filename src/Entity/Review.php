<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReviewRepository")
 */
class Review
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $asin;

    /**
     * @ORM\Column(type="smallint")
     */
    private $rate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $author;

    /**
     * @ORM\Column(type="integer")
     */
    private $helpfulCount = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $verified = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tracking", inversedBy="reviews")
     * @ORM\JoinColumn(name="tracking_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $tracking;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CrawlingUrl", inversedBy="reviews")
     * @ORM\JoinColumn(name="crawling_url_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $crawlingUrl;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateReview;

    /**
     * @ORM\Column(type="smallint")
     */
    private $active = 1;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $reviewId;

    /**
     * @ORM\Column(type="smallint")
     */
    private $isDownloaded = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $isLastUpdate = 0;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param mixed $rate
     * @return Review
     */
    public function setRate(float $rate)
    {
        $this->rate = $rate;

        return $this;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     * @return Review
     */
    public function setAuthor(string $author)
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return Review
     */
    public function setCreatedAt(\DateTimeInterface $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     * @return Review
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     * @return Review
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateReview()
    {
        return $this->dateReview;
    }

    /**
     * @param mixed $dateReview
     * @return Review
     */
    public function setDateReview($dateReview)
    {
        $this->dateReview = $dateReview;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * @param mixed $tracking
     * @return Review
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;
        
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getCrawlingUrl()
    {
        return $this->crawlingUrl;
    }

    /**
     * @param mixed $crawlingUrl
     * @return Review
     */
    public function setCrawlingUrl($crawlingUrl)
    {
        $this->crawlingUrl = $crawlingUrl;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     * @return Review
     */
    public function setActive($active)
    {
        $this->active = $active;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReviewId()
    {
        return $this->reviewId;
    }

    /**
     * @param mixed $reviewId
     * @return Review
     */
    public function setReviewId($reviewId)
    {
        $this->reviewId = $reviewId;
        return $this;
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
     * @return Review
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHelpfulCount()
    {
        return $this->helpfulCount;
    }

    /**
     * @param mixed $helpfulCount
     * @return Review
     */
    public function setHelpfulCount($helpfulCount)
    {
        $this->helpfulCount = $helpfulCount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * @param mixed $verified
     * @return Review
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAsin()
    {
        return $this->asin;
    }

    /**
     * @param mixed $asin
     * @return Review
     */
    public function setAsin($asin)
    {
        $this->asin = $asin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsDownloaded()
    {
        return $this->isDownloaded;
    }

    /**
     * @param mixed $isDownloaded
     * @return Review
     */
    public function setIsDownloaded($isDownloaded)
    {
        $this->isDownloaded = $isDownloaded;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsLastUpdate()
    {
        return $this->isLastUpdate;
    }

    /**
     * @param mixed $isLastUpdate
     * @return Review
     */
    public function setIsLastUpdate($isLastUpdate)
    {
        $this->isLastUpdate = $isLastUpdate;
        
        return $this;
    }

}
