<?php

namespace App\Repository;

use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 *
 * @method Trip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trip[]    findAll()
 * @method Trip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function save(Trip $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Trip $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByFilters($user, $campus, $searchzone, $startDate, $endDate, $organizerTrips, $registeredTrips, $notRegisteredTrips, $pastTrips) {
        $qb = $this->createQueryBuilder('t')
            /*->where('t.campus = :campus')
            ->setParameter('campus', $campus)*/;

        $qb->leftJoin('t.campus', 'campus')
            ->addSelect('campus');

        $qb->leftJoin('t.state', 'state')
            ->addSelect('state');

        $qb->leftJoin('t.organizer', 'organizer')
            ->addSelect('organizer');

        $qb->leftJoin('t.registeredUsers', 'registered')
            ->addSelect('registered');

        $qb->leftJoin('t.place', 'place')
            ->addSelect('place');

        $qb->leftJoin('place.city', 'city')
            ->addSelect('city');
            
            /*if($searchzone) {
                $qb->andWhere('t.name like :searchzone')
                    ->setParameter('searchzone', '%'.$searchzone.'%');
            }*/

            /*if(!$organizerTrips) {
                $qb->andWhere('t.organizer != :user')
                    ->setParameter('user', $user);
            }*/

            /*if($registeredTrips) {
                $qb->leftJoin('t.registeredUsers', 'ru')
                    ->addSelect('ru');
                $qb->andWhere('ru = :user')
                    ->setParameter('user', $user);
            }*/

            /*if(!$pastTrips) {
                $qb->andWhere('t.state != 5');
             }*/

        $query = $qb->getQuery();

        return $query->getResult();
    }

//    /**
//     * @return Trip[] Returns an array of Trip objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Trip
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
