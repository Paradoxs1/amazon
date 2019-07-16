<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\CrawlingUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\Expr\Join as Join;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /*
     * EXAMPLE QUERY:

SELECT *
FROM `product` AS `p`
JOIN `crawling_queue` AS `cq` ON `cq`.`id` = `p`.`crawling_queue_id`
JOIN `crawling_url` AS `cu` ON `cu`.`crawling_queue_id` = `cq`.`id`
WHERE
     `p`.`id` != 2
     AND (`p`.`asin` = 'B077V455KT' OR `p`.`parent_asin` = 'B077V455KT')
     AND `cq`.`parent_id` = 6
     AND `cu`.`status` = 5
ORDER BY `p`.`id` ASC
LIMIT 1

     */
    public function getParsedProduct(CrawlingUrl $crawlingUrl)
    {
        $currentProduct = $crawlingUrl->getCrawlingQueue()->getProduct();
        $parentQueue = $crawlingUrl->getCrawlingQueue()->getParent();

        $qb = $this->createQueryBuilder('p');
        
        $qb
            ->join('p.crawlingQueue', 'cq')
            ->join('cq.crawlingUrls', 'cu')
            ->where($qb->expr()->not(
                $qb->expr()->eq('p.id', ':currentProductId')
            ))
            ->andwhere($qb->expr()->orX(
                $qb->expr()->eq('p.asin', ':asin'),
                $qb->expr()->eq('p.parentAsin', ':asin')
            ))
            ->andWhere($qb->expr()->eq('cq.parent', ':crawling_queue_parent_id'))
            ->andWhere($qb->expr()->eq('cu.updateField', ':update_field'))
            ->andWhere($qb->expr()->eq('cu.status', ':status'))
            ->setParameter('currentProductId', $currentProduct->getId())
            ->setParameter('asin', $currentProduct->getParentAsin())
            ->setParameter('crawling_queue_parent_id', $parentQueue->getId())
            ->setParameter('update_field', $crawlingUrl->getUpdateField())
            ->setParameter('status', CrawlingUrl::getStatusScraped())
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(1);

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCountAllElement()
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id) as Products')
            ->getQuery()
            ->getResult();
    }

}
