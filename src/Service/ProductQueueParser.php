<?php
/**
 * Created by PhpStorm.
 * User: Paradoxs
 * Date: 27.06.2018
 * Time: 14:36
 */

namespace App\Service;

use App\BaseScraperBundle\Service\BaseParser;
use App\Entity\CrawlingQueue;
use App\Entity\CrawlingUrl;

class ProductQueueParser extends BaseParser
{

    public function createCrawlingUrls(CrawlingQueue $crawlingQueue)
    {
        $this->setVarsFromObject($crawlingQueue);

        // Persist and flush first url for scraping product info, cause we need it's ID for next URLs
        $firstUrl = $this->tracking->formFirstUrl();
        $productUrlQueueFirst = $this->persistProductCrawlingUrl($firstUrl);
        $this->em->flush();

        // Create URLs for retrieve reviews count info
        $urlsProductQueue = $this->tracking->formUrls();

        // Persist all CrawlingUrl's to DB
        foreach ($urlsProductQueue as $url) {
            $this->persistProductCrawlingUrl($url, $productUrlQueueFirst->getId());
        }
        // And finally flush all the URLs in one transaction
        $this->em->flush();
    }

    private function persistProductCrawlingUrl($url, $parentId = null)
    {
        $isFirstUrl = empty($parentId);
        $crawlingUrl = new CrawlingUrl();
        
        $crawlingUrl
            ->setUrl($url)
            ->setParentId($parentId)
            ->setUpdateField($crawlingUrl->getReviewsCountMethodName($this->config))
            ->setStatusCreated()
            ->setTypeProduct($isFirstUrl);
        
        if ($parentId) {
            $crawlingUrl->setUpdatedAt(new \DateTime());
        }
        
        $this->crawlingQueue->addCrawlingUrl($crawlingUrl);
        
        $this->em->persist($crawlingUrl);
        $this->em->persist($this->crawlingQueue);

        return $crawlingUrl;
    }

}
