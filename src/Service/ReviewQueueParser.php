<?php

namespace App\Service;

use App\BaseScraperBundle\Service\BaseParser;
use App\Entity\CrawlingQueue;
use App\Entity\CrawlingUrl;
use App\Entity\Review;

class ReviewQueueParser extends BaseParser
{

    public function createCrawlingUrls(CrawlingQueue $crawlingQueue, $update = null)
    {
        $this->setVarsFromObject($crawlingQueue);

        $this->isUpdate = $update;
        
        $url = $this->tracking->formReviewUrl();

        $crawlingUrl = new CrawlingUrl();
        
        $crawlingUrl
            ->setUrl($url)
            ->setStatusCreated()
            ->setTypeReviewQueue($this->isUpdate);

        $this->crawlingQueue->addCrawlingUrl($crawlingUrl);

        $this->em->persist($crawlingUrl);

        $this->em->flush();
    }

    public function parseCrawlingUrl(CrawlingUrl $crawlingUrl)
    {
        $this->setVarsFromObject($crawlingUrl);
        
        $this->isUpdate = $this->crawlingUrl->isUpdate();

        $crawler = $this->getUrlContent($this->crawlingUrl);

        $urlsCreated = $this->createCrawlingUrlForParsedReviews($crawler);

        $this->crawlingUrl->setStatusScraped();
        $this->saveObject($this->crawlingUrl);
        
        if ($this->isUpdate && !$urlsCreated) {
            if ($this->checkUrlsAreScraped()) {            
                $this->setCrawlingQueueScraped();
            }
        }
    }

    private function createCrawlingUrlForParsedReviews($crawler)
    {
        if (!$this->haveNewReviews($crawler)) {
            return false;
        }
        
        $pagination = $crawler->filter('#cm_cr-pagination_bar');
        $pageCount = $this->getTextForFilteredBlock($pagination->filter('.page-button:nth-last-child(2)'));

        // If there are no pages - we need to create at least 1 page link for getting reviews
        $totalReviewCount = ($pageCount !== false) ? $pageCount : 1;

        // Persist review URLs
        for ($i = 1; $i <= $totalReviewCount; $i++) {
            $newPageUrl = $this->tracking->formReviewUrl($i);
            $crawlingUrl = new CrawlingUrl();
            
            $crawlingUrl
                ->setUrl($newPageUrl)
                ->setStatusCreated()
                ->setTypeReview($this->isUpdate);

            $this->crawlingQueue->addCrawlingUrl($crawlingUrl);
        
            $this->em->persist($crawlingUrl);
            $this->em->persist($this->crawlingQueue);
        }

        // And flush all URLs to DB in one transaction
        $this->em->flush();
        
        return true;
    }
    
    private function haveNewReviews($crawler)
    {
        // This checking is valid only for update process
        if (!$this->isUpdate) {
            return true;
        }
        
        $reviewsBlock = $crawler->filter('div[id="cm_cr-review_list"]');

        if (!is_object($reviewsBlock) || !$reviewsBlock->getNode(0)) {
            // No reviews to parse at all
            return false;
        }
        
        $reviews = $reviewsBlock->filter('div[data-hook="review"]');
        
        if ($reviews->getNode(0)) {
            $reviewHtml = $reviews->getNode(0);
            
            $review = $this->getCrawler($reviewHtml);
        
            $reviewId = trim($review->attr('id'));
            
            $reviewOld = $this->em->getRepository(Review::class)->findOneByReviewId($reviewId);
            
            if ($reviewOld instanceof Review) {
                // The first review on page exists, we don't need to create queue
                return false;
            }
        }
        
        return true;
    }

}
