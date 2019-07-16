<?php

namespace App\Repository;

use App\BaseScraperBundle\Repository\BaseCrawlingQueueRepository;
use App\Entity\CrawlingQueue;
use App\Entity\CrawlingUrl;

/**
 * @method CrawlingQueue|null find($id, $lockMode = null, $lockVersion = null)
 * @method CrawlingQueue|null findOneBy(array $criteria, array $orderBy = null)
 * @method CrawlingQueue[]    findAll()
 * @method CrawlingQueue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CrawlingQueueRepository extends BaseCrawlingQueueRepository
{
    /*
     * Example SQL:
SELECT `tracking`.`id`, `tracking`.`asin`, `tracking`.`parent`, `tracking`.`status`, `crawling_queue`.`id` AS `cqId`, `crawling_queue`.`status` AS `cqStatus`, `crawling_queue`.`type` AS `cqType`, `crawling_url`.`id` AS `cuId`, `crawling_url`.`status`AS `cuStatus`, `crawling_url`.`type`AS `cuType` FROM `crawling_queue` LEFT JOIN `tracking` ON `crawling_queue`.`tracking_id` = `tracking`.`id` LEFT JOIN `crawling_url` ON `crawling_url`.`crawling_queue_id` = `crawling_queue`.`id` WHERE `crawling_queue`.`status` >= 13 OR `crawling_url`.`status` >= 13
     */
    public function findTrackingAndFailedUrl($queueId = null, $urlId = null)
    {
        $qb = $this->createQueryBuilder('cq')
            ->select('cq.id as Queue, cq.type as QueueType, cq.status as QueueStatus');

        $qb
            ->leftjoin('cq.tracking', 't')
            ->addSelect('t.id as Tracking, t.asin as Asin, t.parent as Parent, t.status as Status, t.type as Type')
            ->leftjoin('cq.crawlingUrls', 'cu')
            ->addSelect('cu.id as Url, cu.status as UrlStatus, cu.type as UrlType')
            ->where('cq.status >= :cq_status')
            ->orWhere('cu.status >= :cu_status')
            ->setParameter('cq_status', CrawlingQueue::getStatusFailedContinious())
            ->setParameter('cu_status', CrawlingUrl::getStatusFailedContinious());

        if ($queueId) {
            $qb->andWhere('cq.id = :queue_id')
                ->setParameter('queue_id', $queueId);
        }

        if ($urlId) {
            $qb->andWhere('cu.id = :url_id')
                ->setParameter('url_id', $urlId);
        }

        return $qb->getQuery()
            ->getResult();
    }
}
