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

    public function findByActeur($acteur)
    {
        return $this->createQueryBuilder('f')
            ->join('f.acteurs', 'a', 'WITH', 'a.nomPrenom = :acteur')
            ->setParameter('acteur', $acteur)
            ->getQuery()
            ->getResult();
    }
    
    public function findByTitle($film_titre)
    {
        return $this->createQueryBuilder('f')
            ->where('f.titre LIKE :film_titre')
            ->setParameter('film_titre', '%'.$film_titre.'%')
            ->getQuery()
            ->getResult();
    }

    public function findByGenre($genre)
    {
        return $this->createQueryBuilder('f')
            ->join('f.genre', 'g', 'WITH', 'g.nom = :genre')
            ->setParameter('genre', $genre)
            ->getQuery()
            ->getResult();
    }

    public function findByTwoActors($acteur1, $acteur2) {
        return $this->createQueryBuilder('f')
            ->join('f.acteurs', 'a', 'WITH', 'a.nomPrenom = :acteur1')
            ->andWhere('f.acteurs', 'a', 'WITH', 'a.nomPrenom = :acteur2')
            ->setParameter('acteur1',$acteur1)
            ->setParameter('acteur2', $acteur2)
            ->getQuery()
            ->getResult();
            
        //return $this->createQueryBuilder('f')
        

    }
}
