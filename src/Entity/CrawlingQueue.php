<?php

namespace App\Entity;

use App\BaseScraperBundle\Entity\BaseCrawlingQueue;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CrawlingQueueRepository")
 */
class CrawlingQueue extends BaseCrawlingQueue
{

    // List of types for corresponding field
    const
        TYPE_QUEUE_PRODUCTS_PARSE = 2,
        TYPE_QUEUE_REVIEWS_CREATE = 3,
        TYPE_QUEUE_REVIEWS_UPDATE = 4;
    
    // List of statuses for corresponding field
    const
        STATUS_QUEUE_FAILED_REVIEWS_CREATE = 15,
        STATUS_QUEUE_FAILED_PRODUCTS_PARSE = 16,
        STATUS_QUEUE_FAILED_REVIEWS_UPDATE = 17;

    static protected $weeklyProcessTypes = [
        self::TYPE_QUEUE_PRODUCTS_PARSE,
        self::TYPE_QUEUE_REVIEWS_UPDATE
    ];

    static protected $failedStatuses = [
        self::STATUS_QUEUE_FAILED_CONTINIOUS,
        self::STATUS_QUEUE_FAILED_REVIEWS_CREATE,
        self::STATUS_QUEUE_FAILED_PRODUCTS_PARSE,
        self::STATUS_QUEUE_FAILED_REVIEWS_UPDATE,
    ];

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Product", mappedBy="crawlingQueue")
     */
    private $product;

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     * @return CrawlingQueue
     */
    public function setProduct($product)
    {
        $this->product = $product;
        if ($product->getCrawlingQueue() !== $this) {
            $product->setCrawlingQueue($this);
        }
        
        return $this;
    }

    public static function isWeeklyProcessing($type)
    {
        return in_array($type, self::$weeklyProcessTypes);
    }

    public function isFailed()
    {
        return in_array($this->getStatus(), self::getFailedStatuses());
    }

    static public function getFailedStatuses()
    {
        return self::$failedStatuses;
    }
}
