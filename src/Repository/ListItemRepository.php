<?php

namespace App\Repository;

use App\Entity\ListItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ListItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListItem[]    findAll()
 * @method ListItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListItem::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ListItem $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(ListItem $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return ListItem[] Returns an array of ListItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ListItem
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    /**
     * Get list of items by listItem, mode and user
     */
    public function findUserItemsByMode($idUser, $idMode)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT item.name
            FROM App\Entity\Item item
            INNER JOIN App\Entity\Mode mode
            INNER JOIN App\Entity\User user
            INNER JOIN App\Entity\ListItem listItem
            WHERE item.mode = mode.id
            AND listItem.user = user.id
            AND user.id = $idUser
            AND mode.id = $idMode"
        );

        return $query->getResult();
    }

    /**
     * Get list of items by listItem, ordered by ID desc
     */
    public function findAllOrderedByAdded()
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;

    }

    /**
     * Get list of items by user
     */
    public function finByUser($userId)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :val')
            ->setParameter('val', $userId)
            ->getQuery()
            ->getResult()
        ;

    }

    /**
     * @return ListItem[] Returns an array of ListItem objects ordered by Id
     */
    public function findByCreationDate()
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return ListItem[] Returns an array of ListItem objects ordered by Id and user
     */
    public function findByCreationDateAnduser($userId)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :val')
            ->setParameter('val', $userId)
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return ListItem[] Returns an array of ListItem objects ordered by Id and user
     */
    public function findByCreationDateAnduserOld($userId)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :val')
            ->setParameter('val', $userId)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return ListItem[] Returns an array of ListItem objects ordered by name and mode
     */
    public function findByAlphabeticalOrderAndUser($modeId)
    {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.item', 'i')
            ->andWhere('l.user = :val')
            ->setParameter('val', $modeId)
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return ListItem[] Returns an array of ListItem objects ordered by name
     */
    public function findByAlphabeticalOrder()
    {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.item', 'i')
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
