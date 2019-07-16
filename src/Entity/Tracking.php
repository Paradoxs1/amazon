<?php

namespace App\Entity;

use App\BaseScraperBundle\Entity\BaseTracking;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="App\Repository\TrackingRepository")
 */
class Tracking extends BaseTracking
{
    # List of statuses for tracking - this will be used only for review processing
    const
        STATUS_TRACKING_REVIEWS_CREATED = 5;

    protected $starsCombination;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $asin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review", mappedBy="tracking")
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="tracking")
     */
    private $products;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $product_pk;

    /**
     * @return mixed
     */
    public function getAsin()
    {
        return $this->asin;
    }

    /**
     * @param mixed $asin
     * @return Tracking
     */
    public function setAsin($asin)
    {
        $this->asin = $asin;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     * @return Tracking
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return mixed
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @return mixed
     */
    public function getProductPk()
    {
        return $this->product_pk;
    }

    /**
     * @param mixed $product_pk
     * @return Tracking
     */
    public function setProductPk($product_pk)
    {
        $this->product_pk = $product_pk;
        
        return $this;
    }
    
    public function getProductUrl($forReviews = false)
    {
        $endSection = (!$forReviews) ? $this->config->getParameter('review_end_section') : '';
        
        return "https://www.{$this->getRootDomain()}/product-reviews/{$this->getAsin()}/{$endSection}";
    }
    
    public function formReviewUrl($page = null)
    {
        $formatTypeUrlPart = $this->config->getParameter('format_type');
        $sortByUrlPart = $this->config->getParameter('review_sort_by');
        $pageNumberUrlPart = $this->config->getParameter('page_number');
        
        if (empty($page)) {
            $page = 1;
        }
        
        $productUrl = $this->getProductUrl(true);
        $childUrlPart = ($this->isParent()) ? "" : "{$formatTypeUrlPart}&";
        $url = "{$productUrl}?{$childUrlPart}{$sortByUrlPart}&{$pageNumberUrlPart}={$page}";
        
        return $url;
    }
    
    public function isParent()
    {
        return ($this->getAsin() === $this->getParent());
    }
    
    public function formFirstUrl()
    {
        $formatTypeUrlPart = $this->config->getParameter('format_type');
        
        return "{$this->getProductUrl()}?{$formatTypeUrlPart}";
    }
    
    public function formUrls()
    {
        $urlsProductQueue = [];
        
        // Adding parent URLs to Queue
        foreach ($this->getUrlStarsCombination() as $starsCount) {
            // URL for all reviews filtered by stars count
            if ($starsCount != $this->config->getParameter('stars_all')) {
                $urlsProductQueue[] = $this->getProductAmazonUrl($starsCount, false);
            }
            // URL for VERIFIED reviews filtered by stars count
            $urlsProductQueue[] = $this->getProductAmazonUrl($starsCount, false, true);
        }

        // We need to add child URLs to Queue only for trackings that is NOT parent
        if (!$this->isParent()) {
            foreach ($this->getUrlStarsCombination() as $starsCount) {
                // URL for all reviews filtered by stars count for CHILD ASIN ONLY
                $urlsProductQueue[] = $this->getProductAmazonUrl($starsCount, true, false);
                // URL for VERIFIED reviews filtered by stars count for CHILD ASIN ONLY
                $urlsProductQueue[] = $this->getProductAmazonUrl($starsCount, true, true);
            }
        }
        
        return $urlsProductQueue;
    }
    
    private function getProductAmazonUrl($starsCount, $child, $verified = false)
    {
        $productUrl = $this->getProductUrl();
        
        $reviewsTypeVerified = $this->config->getParameter('reviews_type');
        $filterByStar = $this->config->getParameter('stars_param_name');
        $formatType = $this->config->getParameter('format_type');
        
        $url = "{$productUrl}?{$filterByStar}={$starsCount}";
        
        if ($child) {
            $url .= "&{$formatType}";
        }
        if ($verified) {
            $url .= "&{$reviewsTypeVerified}";
        }
        
        return $url;
    }
    
    private function getUrlStarsCombination()
    {
        if (empty($this->starsCombination)) {
            $this->starsCombination = [
                $this->config->getParameter('stars_all'),
                $this->config->getParameter('stars_five'),
                $this->config->getParameter('stars_four'),
                $this->config->getParameter('stars_three'),
                $this->config->getParameter('stars_two'),
                $this->config->getParameter('stars_one')
            ];
        }
        
        return $this->starsCombination;
    }

}