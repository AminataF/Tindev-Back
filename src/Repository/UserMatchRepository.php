<?php

namespace App\Repository;
use App\Entity\User;
use App\Entity\UserMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserMatch>
 *
 * @method UserMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMatch[]    findAll()
 * @method UserMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMatch::class);
    }

    public function add(UserMatch $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserMatch $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOnePendingMatch(User $userMatcher, User $userMatched): ?UserMatch
    {
        $qb = $this->createQueryBuilder('um');
        $qb->where('um.userMatcher = :userMatcher')
           ->andWhere('um.userMatched = :userMatched')
           ->andWhere('um.status = :status')
           ->setParameters([
               'userMatcher' => $userMatcher,
               'userMatched' => $userMatched,
               'status' => 'en attente'
           ]);
        
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findMatchByStatusAndUsers(string $status, User $userMatcher, User $userMatched): ?UserMatch
    {
        return $this->createQueryBuilder('um')
            ->andWhere('um.status = :status')
            ->andWhere('(um.userMatcher = :userMatcher AND um.userMatched = :userMatched) OR (um.userMatcher = :userMatched AND um.userMatched = :userMatcher)')
            ->setParameters([
                'status' => $status,
                'userMatcher' => $userMatcher,
                'userMatched' => $userMatched,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function findMatchByStatusAndInitiatorReceiver($status, $userMatcher, $userMatched)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.status = :status')
            ->andWhere('m.userMatcher = :userMatcher')
            ->andWhere('m.userMatched = :userMatched')
            ->setParameter('status', $status)
            ->setParameter('userMatcher', $userMatcher)
            ->setParameter('userMatched', $userMatched)
            ->getQuery()
            ->getOneOrNullResult();
    }
    


//    /**
//     * @return UserMatch[] Returns an array of UserMatch objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserMatch
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
