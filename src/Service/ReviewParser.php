<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 22.05.18
 * Time: 16:26
 */

namespace App\Service;

use App\BaseScraperBundle\Service\BaseParser;
use App\Entity\CrawlingUrl;
use App\Entity\Review;

class ReviewParser extends BaseParser
{

    public function parseCrawlingUrl(CrawlingUrl $crawlingUrl)
    {
        $this->setVarsFromObject($crawlingUrl);
        
        $this->isUpdate = $this->crawlingUrl->isUpdate();

        $isParsed = $this->parsePageReviews();

        if ($isParsed) {
            $this->crawlingUrl->setStatusScraped();
        } else {
            $this->crawlingUrl->setStatusSkipped();
        }
        $this->saveObject($this->crawlingUrl);
        
        if ($this->checkUrlsAreScraped()) {
            // We parsed all reviews for tracking
            if ($this->isUpdate) {
//            if ($this->isUpdate) {
//                $this->checkReviewsAndDeactivate();
//                $this->resetIsLastUpdateFlags();
//            }
            } else {
                $this->tracking->setStatusReviewsCreated();
                $this->saveObject($this->tracking);
            }
            
            $this->setCrawlingQueueScraped();
        }
    }

    private function parsePageReviews()
    {
        $crawler = $this->getUrlContent($this->crawlingUrl);
        
        $reviewsBlock = $crawler->filter('div[id="cm_cr-review_list"]');

        if (!is_object($reviewsBlock) || !$reviewsBlock->getNode(0)) {
            $exceptionMessage = $this->config->getException('REVIEWS_NOT_FOUND');
//            throw new \Exception($exceptionMessage);
            // If we can't find reviews - just skip url now
            $this->comment("{$exceptionMessage}, skipping url.");
            return false;
        }
        
        $reviewsProcessed = 0;
        
        $reviews = $reviewsBlock->filter('div[data-hook="review"]');
        
        if ($reviews->getNode(0)) {
            foreach ($reviews as $reviewHtml) {
                $continueLoop = $this->processSingleReview($reviewHtml);
                $reviewsProcessed++;
                if (!$continueLoop) {
                    break;
                }
            }
        }
        
        $this->comment("Processed reviews: {$reviewsProcessed}.");
        
        return true;
    }
    
    private function processSingleReview($reviewHtml)
    {
        $return = true;
        $review = $this->getCrawler($reviewHtml);
        
        $reviewId = trim($review->attr('id'));

        // Is parsed review already exist in DB ??
        $reviewOld = $this->em->getRepository(Review::class)->findOneByReviewId($reviewId);

        if ($this->isUpdate) {
            if ($reviewOld instanceof Review) {
                // If yes - set temporary flag for current processing
//                    $reviewOld->setIsLastUpdate(1);
//                    $reviewOld->setUpdatedAt(new \DateTime());
//                    $this->saveObject($reviewOld);

                // We found first review in DB - stop processing others reviews
                $this->deactivateCrawlingUrls();
                $return = false;
            } else {
                // If no - add new review to the DB
                $this->parseReviewAndSave($review);
            }
        } else {
            if ($reviewOld instanceof Review) {
                $this->comment("Duplicate review exists: {$reviewId}, deleting.");
                $this->em->remove($reviewOld);
                $this->em->flush();
            }
            $this->parseReviewAndSave($review);
        }
        
        return $return;
    }

    private function deactivateCrawlingUrls()
    {
        // Find URLs to next pages for updating reviews
        $urls = $this->em->getRepository(CrawlingUrl::class)->findForDeactivate($this->crawlingQueue);
        
        // Persist urls with status skipped
        foreach ($urls as $url) {
            $url->setStatusSkipped();
            $this->em->persist($url);
        }
        // And flush them all
        $this->em->flush();
    }

    private function parseReviewAndSave($review)
    {
        $rating = substr($this->getTextForFilteredBlock($review->filter('span.a-icon-alt')), 0, 3);
        $author = $this->getTextForFilteredBlock($review->filter('a.author'));
        $date = trim(str_replace([',', 'on'], '', $this->getTextForFilteredBlock($review->filter('span[data-hook="review-date"]'))));
        $content = $this->getTextForFilteredBlock($review->filter('span[data-hook="review-body"]'));
        $title = $this->getTextForFilteredBlock($review->filter('a[data-hook="review-title"]'));

        if ($review->filter('.a-row.a-spacing-base')->count()) {
            $helpfulCountArray = explode(' ', $this->getTextForFilteredBlock($review->filter('.a-row.a-spacing-base')));

            $helpfulCount = ($helpfulCountArray[0] == 'One') ? 1 : $helpfulCountArray[0];
        } else {
            $helpfulCount = 0;
        }

        if ($review->filter('span[data-hook="avp-badge"]')->count()) {
            $verified = $this->getTextForFilteredBlock($review->filter('span[data-hook="avp-badge"]'));
        } else {
            $verified = 0;
        }

        $id = trim($review->attr('id'));

        $this->saveReview($title, $rating, $author, $helpfulCount, $verified, $date, $content, $id);
    }

    private function saveReview($title, $rating, $author, $helpfulCount, $verified, $date, $content, $id)
    {
        $tracking = $this->tracking;

        $reviewDate = new \DateTime($date);
        $review = new Review();
        
        $review
            ->setTitle($title)
            ->setAsin($tracking->getAsin())
            ->setRate($rating)
            ->setAuthor($author)
            ->setHelpfulCount($helpfulCount)
            ->setDateReview($reviewDate)
            ->setContent($content)
            ->setTracking($tracking)
            ->setCrawlingUrl($this->crawlingUrl)
            ->setReviewId($id);
        
        if (isset($verified)) {
            $review->setVerified(1);
        }

        // All reviews must be active by default
        $review->setActive(1);

        // And during update process - don't forget about specific flag
//        if ($this->isUpdate) {
//            $review->setIsLastUpdate(1);
//        }

        $this->saveObject($review);
    }

//    private function checkReviewsAndDeactivate()
//    {
//        $reviewsRepository = $this->em->getRepository(Review::class);
//
//        $reviewsNotActive = $reviewsRepository->findBy(
//            ['isLastUpdate' => 0, 'tracking' => $this->tracking->getId()]
//        );
//
//        foreach ($reviewsNotActive as $review) {
//            $review->setActive(0);
//            $this->saveObject($review);
//        }
//    }

//    private function resetIsLastUpdateFlags()
//    {
//        return $this->em->getRepository(Review::class)->resetIsLastUpdateFlags($this->tracking);
//    }
}
