<?php

namespace App\Repository;

use App\Entity\Film;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Film|null find($id, $lockMode = null, $lockVersion = null)
 * @method Film|null findOneBy(array $criteria, array $orderBy = null)
 * @method Film[]    findAll()
 * @method Film[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

    // /**
    //  * @return Film[] Returns an array of Film objects
    //  */
    
    public function findByStartAndEndDate($from, $to)
    {
        return $this->createQueryBuilder('f')
            ->where('f.dateSortie BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to',$to)
            ->getQuery()
            ->getResult();
    }
    

    
    public function findByBeforeDate($before)
    {
        return $this->createQueryBuilder('f')
            ->where('f.dateSortie <= :before')
            ->setParameter('before', $before)
            ->getQuery()
            ->getResult();
    }
    
}
