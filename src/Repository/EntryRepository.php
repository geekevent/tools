<?php


namespace App\Repository;


use App\Entity\Entry;
use App\Entity\Space;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entry::class);
    }

    public function remove(Entry $user)
    {
        $this->_em->remove($user);
    }

    public function persist(Entry $user)
    {
        $this->_em->persist($user);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function getGauge(Space $space): array
    {
        $qb = $this->createQueryBuilder('en');

        return $qb
            ->select('sum(en.value) as gauge')
            ->where('en.time > :today')
            ->andWhere('en.time < :tomorrow')
            ->andWhere('en.space = :space')
            ->setParameter('space', $space)
            ->setParameter('today', (new \DateTime())->format('Y-m-d'))
            ->setParameter('tomorrow', (new \DateTime('+1 days'))->format('Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult()
            ;

    }

    public function getGaugeByMoment(Space $space, \DateTime $date)
    {

        $qb = $this->createQueryBuilder('en');

        return $qb
            ->select('sum(en.value) as gauge, en.moment')
            ->where('en.time > :today')
            ->andWhere('en.time < :tomorrow')
            ->andWhere('en.space = :space')
            ->setParameter('space', $space)
            ->setParameter('today', $date->format('Y-m-d'))
            ->setParameter('tomorrow',$date->modify('+1 day')->format('Y-m-d'))
            ->groupBy('en.moment')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getExtremeEntryDateForSpace(Space $space)
    {
        $qb = $this->createQueryBuilder('en');

        return $qb->select('max(en.time) as maxDate')
            ->addSelect('min(en.time) as minDate')
            ->where('en.space = :space')
            ->setParameter('space', $space)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}