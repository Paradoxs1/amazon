<?php
/**
 * Created by PhpStorm.
 * User: Paradoxs
 * Date: 24.05.2018
 * Time: 10:39
 */

namespace App\Repository;

use App\BaseScraperBundle\Repository\BaseTrackingRepository;
use App\Entity\CrawlingQueue;
use App\Entity\Tracking;

/**
 * @method Tracking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tracking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tracking[]    findAll()
 * @method Tracking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackingRepository extends BaseTrackingRepository
{
    public function findTrackingCount($id = null, $asin = null)
    {
        $tracking = $this->findTrackingAndCountReviews($id, $asin);

        foreach ($tracking as $item) {
            $review = $this->findCrawlingQueueUpdateCount($item['Id'], CrawlingQueue::getTypeReviewsUpdate());
            $reviewUpdateCount[] = $review[0];
            $product = $this->findCrawlingQueueUpdateCount($item['Id'], CrawlingQueue::getTypeProductsParse(), true);
            $productUpdateCount[] = $product[0];
        }

        foreach ($tracking as $keyTracking => $item) {
            foreach ($reviewUpdateCount as $key => $review) {
                if ($item['Id'] ==  $review['Id']) {
                    $tracking[$keyTracking]['Review_update_count'] = $review['Review_update_count'];
                }
            }
            foreach ($productUpdateCount as $key => $product) {
                if ($item['Id'] ==  $product['Id']) {
                    $tracking[$keyTracking]['Product_update_count'] = $product['Product_update_count'];
                }
            }
        }

        return $tracking;
    }

    private function findTrackingAndCountReviews($id, $asin)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.id as Id, t.asin as Asin, t.parent as Parent, t.status as Status, t.type as Type, t.marketplace as Marketplace, t.product_pk as ProductPk, count(r.id) as Reviews')
            ->leftJoin('t.reviews', 'r')
            ->groupBy('t.id');

        if (!empty($id)) {
            $qb->where('t.id = :id')
                ->setParameter('id', $id);
        } elseif (!empty($asin)) {
            $qb
                ->where($qb->expr()->orX(
                    $qb->expr()->eq('t.asin', ':asin'),
                    $qb->expr()->eq('t.parent', ':asin')
                ))
                ->setParameter('asin', $asin);
        }

        return $qb->getQuery()
            ->getResult();

    }

    private function findCrawlingQueueUpdateCount($id, $type, $product_update = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('count(cq.id) as Review_update_count, t.id as Id');
        if ($product_update) {
            $qb->select('count(cq.id) as Product_update_count, t.id as Id');
        }
        return $qb->leftJoin('t.crawlingQueues', 'cq')
            ->where('t.id = :id')
            ->andWhere('cq.type = :type')
            ->setParameter('id', $id)
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }
}