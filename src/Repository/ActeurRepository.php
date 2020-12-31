<?php

namespace App\Repository;

use App\Entity\Acteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Acteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Acteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Acteur[]    findAll()
 * @method Acteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Acteur::class);
    }

    // /**
    //  * @return Acteur[] Returns an array of Acteur objects
    //  */

    public function findByName($acteur_name)
    {
        return $this->createQueryBuilder('a')
            ->where('a.nomPrenom LIKE :acteur_name')
            ->setParameter('acteur_name', '%'.$acteur_name.'%')
            ->getQuery()
            ->getResult();
    }

    
    public function findByFilm($titre)
    {
        return $this->createQueryBuilder('a')
            ->join('a.films', 'f', 'WITH', 'f.titre= :titre')
            ->setParameter('titre', $titre)
            ->getQuery()
            ->getResult();
    }
    
}
