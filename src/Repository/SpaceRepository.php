<?php


namespace App\Repository;


use App\Entity\Entry;
use App\Entity\Space;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Space|null find($id, $lockMode = null, $lockVersion = null)
 */
class SpaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Space::class);
    }

    public function remove(Space $user)
    {
        $this->_em->remove($user);
    }

    public function persist(Space $user)
    {
        $this->_em->persist($user);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function getActiveSpaces(): array
    {
        $qb = $this->createQueryBuilder('sp');
        return $qb
            ->addSelect('sum(en.value) as gauge')
            ->innerJoin('sp.event', 'e')
            ->innerJoin(Entry::class, 'en', Join::WITH, 'en.space = sp.id')
            ->where('e.startDate <= :now')
            ->andWhere('e.endDate >= :now')
            ->setParameter('now', (new \DateTime())->format('Y-m-d'))
            ->getQuery()
            ->execute()
            ;
    }
}