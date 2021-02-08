<?php

namespace App\Repository;

use App\Entity\Vacation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

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
            ->leftJoin('v.participants', 'p')
            ->join('v.state', 's')
            ->addSelect('p')
            ->addSelect('s')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCampus($campus)
    {
        return $this->createQueryBuilder('v')

            ->where('v.campus = :val' )
            ->leftJoin('v.participants', 'p')
            ->join('v.state', 's')
            ->addSelect('p')
            ->addSelect('s')
            ->setParameter('val', $campus)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findBookedByCampusUser($campus, $user): array
    {
        return $this->createQueryBuilder('v')
            ->addSelect('p')
            ->andWhere('v.campus = :campus')
            ->andWhere('p.id= :user')
            ->setParameter('campus', $campus)
            ->setParameter('user', $user)
            ->join('v.participants', 'p')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findNotBookedByCampusUser($campus, $user): array
    {
        $qb  = $this->_em->createQueryBuilder();
        $qb2 = $qb;
        $qb2->select('vac.id')
            ->from('app\entity\user', 'u')
            ->leftJoin('u.vacations', 'vac')
            ->where('u.id = ?1');

        $qb  = $this->_em->createQueryBuilder();
        $qb->select('v')
            ->from('App:Vacation', 'v')
            ->where('v.campus = :campus')
            ->setParameter('campus', $campus)
            ->andWhere($qb->expr()->notIn('v.id', $qb2->getDQL())
            );
        $qb->setParameter(1, $user);
        $query  = $qb->getQuery();
        return $query->getResult();
    }

    public function findByWord($campus, $name): array
    {
            $qb = $this->createQueryBuilder('v');
            $qb->where('v.campus = :campus')
                ->andWhere($qb->expr()->like('v.name', ':name'))
                ->setParameter('name', '%'.$name.'%')
                ->setParameter('campus', $campus)
                ->leftJoin('v.participants', 'p')
                ->join('v.state', 's')
                ->addSelect('p')
                ->addSelect('s');

                $query = $qb->getQuery();

              return  $query->getResult();

    }

    public function findByDateMin($campus,$dateMin): array
    {
        $dateMin = new \DateTime($dateMin);
        $dateMin->format('Y-m-d H:i:s');
        return $this->createQueryBuilder('v')
            ->andWhere('v.campus = :campus')
            ->andWhere('v.vacation_date > :dateMin')
            ->setParameter('campus', $campus)
            ->setParameter('dateMin', $dateMin)
            ->leftJoin('v.participants', 'p')
            ->addSelect('p')
            ->join('v.state', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByDateMax($campus,$dateMax): array
    {
        $dateMax = new \DateTime($dateMax);
        $dateMax->format('Y-m-d H:i:s');
        return $this->createQueryBuilder('v')
            ->andWhere('v.campus = :campus')
            ->andWhere('v.vacation_date < :dateMax')
            ->leftJoin('v.participants', 'p')
            ->addSelect('p')
            ->setParameter('campus', $campus)
            ->setParameter('dateMax', $dateMax)
            ->join('v.state', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findFilteredVacations(\App\Entity\PropertySearch $search): array
    {
        $qb  = $this->_em->createQueryBuilder();
        $qb2 = $qb;
        $qb2->select('vac.id')
            ->from('app\entity\user', 'u')
            ->leftJoin('u.vacations', 'vac');


        $qb = $this->_em->createQueryBuilder();
        $qb->select('v')
            ->from('App:Vacation', 'v')
            ->where('v.campus = :campus')
            ->setParameter('campus', $search->getCampus())
            ->join('v.state', 's')
            ->addSelect('s')
            ->leftJoin('v.participants', 'p')
            ->addSelect('p');

        if ($search->getHost()){
            $qb->andWhere('v.organiser = :organiser')
                ->setParameter('organiser', $search->getHost());
        }

        if ($search->getBooked() && $search->getNotBooked() && empty($search->getHost())) {
          $qb->andWhere("v.organiser = :organiser")
              ->setParameter('organiser', $search->getBooked());
        }
            else if($search->getBooked() && empty($search->getNotBooked())) {
                $qb->andWhere('p = :participants')
                    ->setParameter('participants', $search->getBooked());
            } else if ($search->getBooked() && $search->getNotBooked() && $search->getHost()) {
            }



        if (empty($search->getNotBooked()) || empty($search->getBooked()) )
        {
            $qb2->where('u.id = ?1');
            $qb->andWhere($qb->expr()->notIn('v.id', $qb2->getDQL()))
                ->setParameter(1, $search->getNotBooked());
        }


        $query  = $qb->getQuery();
        return $query->getResult();
    }



}
