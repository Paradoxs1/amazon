<?php

namespace App\Entity;

use App\BaseScraperBundle\Entity\BaseCrawlingUrl;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CrawlingUrlRepository")
 */
class CrawlingUrl extends BaseCrawlingUrl
{

    // List of types for corresponding field
    const
        TYPE_URL_REVIEW_QUEUE = 15,
        TYPE_URL_REVIEW = 13,
        TYPE_URL_PRODUCT_QUEUE = 10,
        TYPE_URL_PRODUCT = 8,
        TYPE_URL_REVIEW_UPDATE_QUEUE = 5,
        TYPE_URL_REVIEW_UPDATE = 3;

    protected $updateTypes = [
        self::TYPE_URL_REVIEW_UPDATE,
        self::TYPE_URL_REVIEW_UPDATE_QUEUE
    ];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $updateField;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review", mappedBy="crawlingUrl")
     */
    private $reviews;
    
    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        
        parent::__construct();
    }

    public function setTypeReviewQueue($isUpdate)
    {
        $type = ($isUpdate) ? self::TYPE_URL_REVIEW_UPDATE_QUEUE : self::TYPE_URL_REVIEW_QUEUE;
        
        return $this->setType($type);
    }
    
    public function setTypeReview($isUpdate)
    {
        $type = ($isUpdate) ? self::TYPE_URL_REVIEW_UPDATE : self::TYPE_URL_REVIEW;
        
        return $this->setType($type);
    }
    
    public function setTypeProduct($isFirstUrl)
    {
        $type = ($isFirstUrl) ? self::TYPE_URL_PRODUCT_QUEUE : self::TYPE_URL_PRODUCT;
        
        return $this->setType($type);
    }

    /**
     * @return mixed
     */
    public function getUpdateField()
    {
        return $this->updateField;
    }

    /**
     * @param mixed $updateField
     * @return CrawlingUrl
     */
    public function setUpdateField($updateField)
    {
        $this->updateField = $updateField;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReviews()
    {
        return $this->reviews;
    }
    
    public function isChildMethod($methodName)
    {
        return strripos($methodName, self::METHOD_URL_CHILD);
    }

    public function isUpdate()
    {
        return in_array($this->getType(), $this->updateTypes);
    }
    
    public function getParserName()
    {
        $parserName = null;
        
        switch ($this->getType()) {
            case self::TYPE_URL_PRODUCT:
            case self::TYPE_URL_PRODUCT_QUEUE:
                $parserName = \App\Service\ProductParser::class;
                break;
            case self::TYPE_URL_REVIEW:
            case self::TYPE_URL_REVIEW_UPDATE:
                $parserName = \App\Service\ReviewParser::class;
                break;
            case self::TYPE_URL_REVIEW_QUEUE:
            case self::TYPE_URL_REVIEW_UPDATE_QUEUE:
                $parserName = \App\Service\ReviewQueueParser::class;
                break;
        }
        
        return $parserName;
    }
    
    public function getReviewsCountMethodName($config)
    {
        $methodName = false;
        $this->config = $config;

        $urlArray = parse_url($this->getUrl());

        if (is_array($urlArray)) {
            parse_str($urlArray['query'], $queryStringArray);
            $filterByStar = $config->getParameter('stars_param_name');
            
            if (is_array($queryStringArray) && isset($queryStringArray[$filterByStar])) {
                $paramValue = $queryStringArray[$filterByStar];

                $starsCount = strstr($paramValue, '_', true);
                if ($starsCount) {
                    $verified = ($this->isProductVerifiedUrl()) ? self::METHOD_URL_VERIFIED : null;
                    $child = ($this->isProductChildUrl()) ? self::METHOD_URL_CHILD : null;
                    $starsName = ($paramValue == $config->getParameter('stars_all')) ? 'ReviewCount' : ucfirst($starsCount);
                    
                    $methodName = 'Total' . $child . $starsName . $verified;
                }
            }
        }
        
        return $methodName;
    }
    
    public function isProductVerifiedUrl()
    {
        $reviewsTypeUrlPart = $this->config->getParameter('reviews_type');
        
        return (stripos($this->getUrl(), $reviewsTypeUrlPart));
    }
    
    public function isProductChildUrl()
    {
        $formatTypeUrlPart = $this->config->getParameter('format_type');
        
        return (stripos($this->getUrl(), $formatTypeUrlPart));
    }

}