<?php

namespace App\Repository;

use App\Entity\Review;
use App\Entity\Tracking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function resetIsLastUpdateFlags(Tracking $tracking){
        $qb = $this->createQueryBuilder('review')
            ->update()
            ->set('review.isLastUpdate', 0)
            ->where('review.isLastUpdate = :isLastUpdate')
            ->andWhere('review.tracking = :tracking')
            ->setParameter('isLastUpdate', 1)
            ->setParameter('tracking', $tracking->getId())
            ->getQuery()
            ->execute();
        
        return $qb;
    }

    public function findCountAllElement()
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id) as Reviews')
            ->getQuery()
            ->getResult();
    }

}
