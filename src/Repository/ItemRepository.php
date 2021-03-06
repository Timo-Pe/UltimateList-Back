<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\ListItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Item $entity, bool $flush = true): void
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
    public function remove(Item $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Item[] Returns an array of Item objects
     */
    public function findAllOrderById()
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.id', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Item[] Returns an array of Item objects
     * 
     */
    public function findAllExceptInListItem($userId)
    {
        return $this->createQueryBuilder('i')   
            ->leftJoin('App\Entity\ListItem', 'li', 'WITH', 'li.item = i.id')
            ->where('i.id NOT IN (
                        SELECT item.id
                        FROM App\Entity\Item item
                        INNER JOIN App\Entity\ListItem list
                        WHERE list.item = item.id
                        AND list.user = :user
                    )')
            ->setParameter('user', $userId)
            ->orderBy('i.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get list of items by mode
     */
    public function findByMode($modeId)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.mode = :val')
            ->setParameter('val', $modeId)
            ->getQuery()
            ->getResult()
        ;

    }

    /**
     * @return Item[] Returns an array of Item objects ordered by Id
     */
    public function findByCreationDate()
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Item[] Returns an array of Item objects ordered by Id and Mode
     */
    public function findByCreationDateAndMode($modeId)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.mode = :val')
            ->setParameter('val', $modeId)
            ->orderBy('i.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Item[] Returns an array of Item objects ordered by Id and Mode
     */
    public function findByCreationDateAndModeOld($modeId)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.mode = :val')
            ->setParameter('val', $modeId)
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Item[] Returns an array of Item objects ordered by name and mode
     */
    public function findByAlphabeticalOrderAndMode($modeId)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.mode = :val')
            ->setParameter('val', $modeId)
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Item[] Returns an array of Item objects ordered by name
     */
    public function findByAlphabeticalOrder()
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}

//SELECT item.*
//FROM item
//LEFT JOIN list_item
//ON list_item.item_id = item.id
//WHERE item.id NOT IN 
//(SELECT list_item.item_id
//FROM list_item
//WHERE list_item.user_id = 1)


