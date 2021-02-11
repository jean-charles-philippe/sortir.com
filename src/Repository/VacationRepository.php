<?php

namespace App\Repository;

use App\Entity\PropertySearch;
use App\Entity\Vacation;
use DateTime;
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



    public function findByCampus($campus)
    {
        return $this->createQueryBuilder('v')

            ->where('v.campus = :val' )
            ->leftJoin('v.participants', 'p')
            ->join('v.state', 's')
            ->join('v.location', 'l')
            ->join('l.city', 'c')
            ->addSelect('p')
            ->addSelect('s')
            ->addSelect('l')
            ->addSelect('c')
            ->setParameter('val', $campus)
            ->getQuery()
            ->getResult()
            ;
    }


    public function findFilteredVacations(PropertySearch $search): array
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



        if ($search->getHost() && empty($search->getBooked()) && empty($search->getNotBooked())){
            $qb->andWhere('v.organiser = :organiser')
                ->setParameter('organiser', $search->getHost());
        }

        if ($search->getBooked() && $search->getNotBooked() && empty($search->getHost()) && empty($search->getFinished())) {

            $qb->andWhere("v.organiser != :organiser")
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


        if ($search->getFinished() && (empty($search->getBooked()) || empty($search->getNotBooked()) || empty($search->getHost())) )
        {
            $qb->andWhere('v.vacation_date < :now' )
            ->setParameter('now', new DateTime("now"));
        }
        else if ($search->getFinished() && $search->getBooked() && $search->getNotBooked() && $search->getHost() ){

        }else if ($search->getFinished() && ($search->getBooked() && $search->getNotBooked())){
            $qb->andWhere('v.vacation_date < :now' )
                ->setParameter('now', new DateTime("now"));
        }



        if ($search->getWord())
        {
            $qb->andWhere($qb->expr()->like('v.name', ':name'))
                ->setParameter('name', '%'.$search->getWord().'%');
        }


        if ($search->getDateMin() && empty($search->getDatemax()))
        {
            $dateMin = new DateTime($search->getDateMin());
            $dateMin->format('Y-m-d H:i:s');
            $qb->andWhere('v.vacation_date > :dateMin')
                ->setParameter('dateMin', $dateMin);
        }

        if ($search->getDatemax() && empty($search->getDateMin()))
        {
            $dateMax = new DateTime($search->getDatemax());
            $dateMax->format('Y-m-d H:i:s');
            $qb->andWhere('v.vacation_date < :dateMax')
                ->setParameter('dateMax', $dateMax);
        }

        if ($search->getDateMin() && $search->getDatemax())
        {
            $dateMin = new DateTime($search->getDateMin());
            $dateMin->format('Y-m-d H:i:s');
            $dateMax = new DateTime($search->getDatemax());
            $dateMax->format('Y-m-d H:i:s');
            $qb->andWhere($qb->expr()->between('v.vacation_date',':dateMin',':dateMax'))
                ->setParameter('dateMin', $dateMin)
                ->setParameter('dateMax', $dateMax);
        }

        $query  = $qb->getQuery();
        return $query->getResult();
    }



}
