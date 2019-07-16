<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
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
    private $asin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalReviewCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalReviewCountVerified = 0;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalStarRating = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalFive = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalFiveVerified = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalFour = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalFourVerified = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalThree = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalThreeVerified = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalTwo = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalTwoVerified = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalOne = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalOneVerified = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalChildReviewCount = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalChildReviewCountVerified = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalChildFive = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalChildFiveVerified = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalChildFour = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalChildFourVerified = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalChildThree = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalChildThreeVerified = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalChildTwo = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalChildTwoVerified = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalChildOne = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalChildOneVerified = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $isDownloaded = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tracking", inversedBy="product")
     * @ORM\JoinColumn(name="tracking_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $tracking;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\CrawlingQueue", inversedBy="product")
     * @ORM\JoinColumn(name="crawling_queue_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $crawlingQueue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parentAsin;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;
    
    /**
     * @ORM\OneToMany(targetEntity="CrawlingUrl", mappedBy="product")
     */
    protected $crawlingUrls;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAsin()
    {
        return $this->asin;
    }

    public function setAsin(string $asin)
    {
        $this->asin = $asin;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalReviewCount()
    {
        return $this->totalReviewCount;
    }

    /**
     * @param mixed $totalReviewCount
     * @return Product
     */
    public function setTotalReviewCount($totalReviewCount)
    {
        $this->totalReviewCount = $totalReviewCount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalRating()
    {
        return $this->totalStarRating;
    }

    /**
     * @param mixed $crawlingUrls
     * @return Product
     */
    public function setCrawlingUrls($crawlingUrls)
    {
        $this->crawlingUrls = $crawlingUrls;
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
     * @return Product
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCrawlingQueue()
    {
        return $this->crawlingQueue;
    }

    /**
     * @param mixed $crawlingQueue
     */
    public function setCrawlingQueue($crawlingQueue)
    {
        $this->crawlingQueue = $crawlingQueue;
        if ($crawlingQueue->getProduct() !== $this) {
            $crawlingQueue->setProduct($this);
        }
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalReviewCountVerified()
    {
        return $this->totalReviewCountVerified;
    }

    /**
     * @param mixed $totalReviewCountVerified
     * @return Product
     */
    public function setTotalReviewCountVerified($totalReviewCountVerified)
    {
        $this->totalReviewCountVerified = $totalReviewCountVerified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalStarRating()
    {
        return $this->totalStarRating;
    }

    /**
     * @param mixed $totalStarRating
     * @return Product
     */
    public function setTotalStarRating($totalStarRating)
    {
        $this->totalStarRating = $totalStarRating;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalFive()
    {
        return $this->totalFive;
    }

    /**
     * @param mixed $totalFive
     * @return Product
     */
    public function setTotalFive($totalFive)
    {
        $this->totalFive = $totalFive;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalFiveVerified()
    {
        return $this->totalFiveVerified;
    }

    /**
     * @param mixed $totalFiveVerified
     * @return Product
     */
    public function setTotalFiveVerified($totalFiveVerified)
    {
        $this->totalFiveVerified = $totalFiveVerified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalFour()
    {
        return $this->totalFour;
    }

    /**
     * @param mixed $totalFour
     * @return Product
     */
    public function setTotalFour($totalFour)
    {
        $this->totalFour = $totalFour;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalFourVerified()
    {
        return $this->totalFourVerified;
    }

    /**
     * @param mixed $totalFourVerified
     * @return Product
     */
    public function setTotalFourVerified($totalFourVerified)
    {
        $this->totalFourVerified = $totalFourVerified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalThree()
    {
        return $this->totalThree;
    }

    /**
     * @param mixed $totalThree
     * @return Product
     */
    public function setTotalThree($totalThree)
    {
        $this->totalThree = $totalThree;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalThreeVerified()
    {
        return $this->totalThreeVerified;
    }

    /**
     * @param mixed $totalThreeVerified
     * @return Product
     */
    public function setTotalThreeVerified($totalThreeVerified)
    {
        $this->totalThreeVerified = $totalThreeVerified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalTwo()
    {
        return $this->totalTwo;
    }

    /**
     * @param mixed $totalTwo
     * @return Product
     */
    public function setTotalTwo($totalTwo)
    {
        $this->totalTwo = $totalTwo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalTwoVerified()
    {
        return $this->totalTwoVerified;
    }

    /**
     * @param mixed $totalTwoVerified
     * @return Product
     */
    public function setTotalTwoVerified($totalTwoVerified)
    {
        $this->totalTwoVerified = $totalTwoVerified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalOne()
    {
        return $this->totalOne;
    }

    /**
     * @param mixed $totalOne
     * @return Product
     */
    public function setTotalOne($totalOne)
    {
        $this->totalOne = $totalOne;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalOneVerified()
    {
        return $this->totalOneVerified;
    }

    /**
     * @param mixed $totalOneVerified
     * @return Product
     */
    public function setTotalOneVerified($totalOneVerified)
    {
        $this->totalOneVerified = $totalOneVerified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalChildReviewCount()
    {
        return $this->totalChildReviewCount;
    }

    /**
     * @param mixed $totalChildReviewCount
     * @return Product
     */
    public function setTotalChildReviewCount($totalChildReviewCount)
    {
        $this->totalChildReviewCount = $totalChildReviewCount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalChildReviewCountVerified()
    {
        return $this->totalChildReviewCountVerified;
    }

    /**
     * @param mixed $totalChildReviewCountVerified
     * @return Product
     */
    public function setTotalChildReviewCountVerified($totalChildReviewCountVerified)
    {
        $this->totalChildReviewCountVerified = $totalChildReviewCountVerified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalChildFive()
    {
        return $this->totalChildFive;
    }

    /**
     * @param mixed $totalChildFive
     * @return Product
     */
    public function setTotalChildFive($totalChildFive)
    {
        $this->totalChildFive = $totalChildFive;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalChildFiveVerified()
    {
        return $this->totalChildFiveVerified;
    }

    /**
     * @param mixed $totalChildFiveVerified
     * @return Product
     */
    public function setTotalChildFiveVerified($totalChildFiveVerified)
    {
        $this->totalChildFiveVerified = $totalChildFiveVerified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalChildFour()
    {
        return $this->totalChildFour;
    }

    /**
     * @param mixed $totalChildFour
     * @return Product
     */
    public function setTotalChildFour($totalChildFour)
    {
        $this->totalChildFour = $totalChildFour;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalChildFourVerified()
    {
        return $this->totalChildFourVerified;
    }

    /**
     * @param mixed $totalChildFourVerified
     * @return Product
     */
    public function setTotalChildFourVerified($totalChildFourVerified)
    {
        $this->totalChildFourVerified = $totalChildFourVerified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalChildThree()
    {
        return $this->totalChildThree;
    }

    /**
     * @param mixed $totalChildThree
     * @return Product
     */
    public function setTotalChildThree($totalChildThree)
    {
        $this->totalChildThree = $totalChildThree;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalChildThreeVerified()
    {
        return $this->totalChildThreeVerified;
    }

    /**
     * @param mixed $totalChildThreeVerified
     * @return Product
     */
    public function setTotalChildThreeVerified($totalChildThreeVerified)
    {
        $this->totalChildThreeVerified = $totalChildThreeVerified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalChildTwo()
    {
        return $this->totalChildTwo;
    }

    /**
     * @param mixed $totalChildTwo
     * @return Product
     */
    public function setTotalChildTwo($totalChildTwo)
    {
        $this->totalChildTwo = $totalChildTwo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalChildTwoVerified()
    {
        return $this->totalChildTwoVerified;
    }

    /**
     * @param mixed $totalChildTwoVerified
     * @return Product
     */
    public function setTotalChildTwoVerified($totalChildTwoVerified)
    {
        $this->totalChildTwoVerified = $totalChildTwoVerified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalChildOne()
    {
        return $this->totalChildOne;
    }

    /**
     * @param mixed $totalChildOne
     * @return Product
     */
    public function setTotalChildOne($totalChildOne)
    {
        $this->totalChildOne = $totalChildOne;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalChildOneVerified()
    {
        return $this->totalChildOneVerified;
    }

    /**
     * @param mixed $totalChildOneVerified
     * @return Product
     */
    public function setTotalChildOneVerified($totalChildOneVerified)
    {
        $this->totalChildOneVerified = $totalChildOneVerified;
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
     * @return Product
     */
    public function setIsDownloaded($isDownloaded)
    {
        $this->isDownloaded = $isDownloaded;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentAsin()
    {
        return $this->parentAsin;
    }

    /**
     * @param mixed $parentAsin
     * @return Product
     */
    public function setParentAsin($parentAsin)
    {
        $this->parentAsin = $parentAsin;
        
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getCrawlingUrls()
    {
        return $this->crawlingUrls;
    }
    
}
