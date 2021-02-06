<?php

namespace App\Repository;

use App\Entity\Vacation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vacation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vacation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vacation[]    findAll()
 * @method Vacation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VacationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vacation::class);
    }

     /**
     * @return Vacation[] Returns an array of Vacation objects
     */
    public function findByCampusAndDateFinished($campus): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.campus = :val', 'v.vacation_date < :val1' )
            ->setParameters(array('val'=> $campus, 'val1' => new \DateTime("now")))
            ->getQuery()
            ->getResult()
        ;
    }



    public function findByWithOrganiser($value): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.campus = :val')
            ->setParameter('val', $value)
            ->addSelect('p')
            ->join('v.participants', 'p')
            ->getQuery()
            ->getResult()
        ;
    }

}
