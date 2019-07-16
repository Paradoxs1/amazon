<?php

/**
 * Created by PhpStorm.
 * User: Paradoxs
 * Date: 07.06.2018
 * Time: 9:56
 */

namespace App\Repository;

use App\BaseScraperBundle\Repository\BaseCrawlingUrlRepository;
use App\Entity\CrawlingUrl;

/**
 * @method CrawlingUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method CrawlingUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method CrawlingUrl[]    findAll()
 * @method CrawlingUrl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CrawlingUrlRepository extends BaseCrawlingUrlRepository
{
    public function checkQueueIsScraped(CrawlingUrl $url)
    {
        $crawlingQueue = $url->getCrawlingQueue();
        $type = $url->getType();

        $urlsTotal = $this->countByQueueOtTypeOrStatus($crawlingQueue, $type);

        $scrapedStatuses = [
            CrawlingUrl::getStatusScraped()
        ];

        $skippedUrlTypes = [CrawlingUrl::getTypeReviewUpdate(), CrawlingUrl::getTypeReview()];
        if (in_array($url->getType(), $skippedUrlTypes)) {
            $scrapedStatuses[] = CrawlingUrl::getStatusSkipped();
        }

        $qb = $this->createQueryBuilder('cu');

        $urlsScraped = $this->countByQueueOtTypeOrStatus($crawlingQueue, $type, $scrapedStatuses);

        return $urlsTotal === $urlsScraped;
    }

    public function findCrawlingUrlId($id)
    {
        return $this->createQueryBuilder('cu')
            ->select('cu.id as Id, cu.url as Url, cu.status as UrlStatus, cu.type as UrlType, cu.parentId as ParentId, cu.updateField as UpdateField, cu.createdAt as TimeCreated, cu.updatedAt as TimeUpdated')
            ->leftJoin('cu.crawlingQueue', 'cq')
            ->addSelect('cq.type as QueueType')
            ->where('cu.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}
