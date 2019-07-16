<?php
/**
 * Created by PhpStorm.
 * User: Paradoxs
 * Date: 27.06.2018
 * Time: 14:36
 */

namespace App\Service;

use App\BaseScraperBundle\Service\BaseParser;
use App\Entity\Product;
use App\Entity\CrawlingUrl;

class ProductParser extends BaseParser
{

    protected $product;

    public function parseCrawlingUrl(CrawlingUrl $crawlingUrl)
    {
        $this->setVarsFromObject($crawlingUrl);

        if ($this->isFirstUrl()) {
            $this->processFirstUrl();
        } else {
            $this->product = $this->crawlingQueue->getProduct();
            if (is_object($this->product)) {
                $this->updateProductByUrl();
            } else {
                throw new Exception($this->config->getException('PRODUCT_NOT_FOUND'));
            }
        }
        
        $this->crawlingUrl->setStatusScraped();
        $this->saveObject($this->crawlingUrl);
            
        if (!$this->isFirstUrl() && $this->checkUrlsAreScraped()) {
            $this->setCrawlingQueueScraped();
        }
    }

    private function isFirstUrl()
    {
        return empty($this->crawlingUrl->getParentId());
    }

    private function processFirstUrl()
    {
        $crawler = $this->getUrlContent($this->crawlingUrl);

        $reviewStarBlock = $crawler->filter('#cm_cr-product_info')->filter('.reviewNumericalSummary ');
        $totalReviewCount = $this->getTotalReviewCount($reviewStarBlock);
        $totalStarRating = $this->getTotalStarRating($reviewStarBlock);

        $product = new Product();
        
        $product
            ->setAsin($this->tracking->getAsin())
            ->setParentAsin($this->tracking->getParent())
            ->setTracking($this->tracking)
            ->setCrawlingQueue($this->crawlingQueue)
            ->setTotalReviewCount($totalReviewCount)
            ->setTotalStarRating($totalStarRating);

        $this->em->persist($this->crawlingQueue);
        $this->em->persist($product);
        $this->em->flush();
    }

    private function getReviewsCount($reviewStarBlock)
    {
        $total = $this->getTextForFilteredBlock($reviewStarBlock->filter('.a-size-base'));

        if ($total !== false) {
            $reviewsCount = str_replace(',', '', explode(' ', trim(substr($total, strpos($total, 'of') + 2)))[0]);
        } else {
            $reviewsCount = 0;
        }

        return $reviewsCount;
    }

    private function getTotalReviewCount($reviewStarBlock)
    {
        $countBlock = $this->getTextForFilteredBlock($reviewStarBlock->filter('.averageStarRatingIconAndCount .totalReviewCount'));

        if ($countBlock !== false) {
            $totalReviewCount = str_replace(',', '', $countBlock);
        } else {
            $totalReviewCount = 0;
        }

        return $totalReviewCount;
    }

    private function getTotalStarRating($reviewStarBlock)
    {
        $totalStarText = $this->getTextForFilteredBlock($reviewStarBlock->filter('.arp-rating-out-of-text'));

        if ($totalStarText !== false) {
            $totalStarRating = substr($totalStarText, 0, 3);
        } else {
            $totalStarRating = 0;
        }

        return $totalStarRating;
    }

    private function updateProductByUrl()
    {
        // We need to dynamically know what value we should update for product
        $setMethodName = $this->getReviewsCountSetMethodName($this->product);

        // If we don't parse field for children and If product isn't parent, i.e. product is children
        if (!$this->crawlingUrl->isChildMethod($setMethodName) && !$this->tracking->isParent()) {
            // Try to find parent product for it and corresponding value for current field
            $parentProduct = $this->findParsedProduct();

            if (is_object($parentProduct)) {
                // And get the the value from product with the same parent instead of amazon query
                $getMethodName = str_replace('set', 'get', $setMethodName);

                $this->product->{$setMethodName}($parentProduct->{$getMethodName}());
                $this->comment("Copied value from product {$parentProduct->getId()}.");
            } else {
                $this->productParseTotalAndPersist($setMethodName);
            }
        } else {
            $this->productParseTotalAndPersist($setMethodName);
        }

        $this->product->setUpdatedAt(new \DateTime());
        $this->saveObject($this->product);
    }
    
    private function findParsedProduct()
    {
        $parsedProduct = $this->em->getRepository(Product::class)->getParsedProduct($this->crawlingUrl);

        return $parsedProduct;
    }

    private function productParseTotalAndPersist($methodName)
    {
        $crawler = $this->getUrlContent($this->crawlingUrl);

        if ($crawler->filter('#cm_cr-review_list')->count()) {
            $reviewStar = $crawler->filter('#cm_cr-review_list .a-section.a-spacing-medium');
            $total = $this->getReviewsCount($reviewStar);
            $this->product->{$methodName}($total);
        }
    }

    private function getReviewsCountSetMethodName(Product $product)
    {
        $methodName = "set{$this->crawlingUrl->getReviewsCountMethodName($this->config)}";
                
        if (!method_exists($product, $methodName)) {
            throw new \Exception($this->config->getException('METHODNAME_INVALID') . ': ' . $methodName);
        }

        return $methodName;
    }

}
