<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findQuantity($id) {
        $qb = $this->createQueryBuilder('p')
        ->select('p.quantity')
        ->where('p.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getSingleScalarResult();

    return $qb;
    
    }
    public function findUnit($id) {
        $qb = $this->createQueryBuilder('p')
        ->select('p.unit')
        ->where('p.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getSingleScalarResult()
    ;

    return $qb;
    }
    
    public function findPrice($id) {
        $qb = $this->createQueryBuilder('p')
        ->select('p.price')
        ->where('p.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getSingleScalarResult()
    ;

    return $qb;

    }

    public function findProductRef($id) {
        $qb = $this->createQueryBuilder('p')
        ->select('p.product_ref')
        ->where('p.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getSingleScalarResult()
    ;

    return $qb;
    }

    public function findName($id) {
        $qb = $this->createQueryBuilder('p')
            ->select('p.name')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $qb;

    }
    public function findImg($id) {
        $qb = $this->createQueryBuilder('p')
            ->select('p.image')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $qb;

    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
