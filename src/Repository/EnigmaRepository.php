<?php


namespace App\Repository;


use App\Entity\Enigma;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Enigma|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Enigma[] findAll()
 * @method Enigma[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 */
class EnigmaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enigma::class);
    }

    public function remove(Enigma $user)
    {
        $this->_em->remove($user);
    }

    public function persist(Enigma $user)
    {
        $this->_em->persist($user);
    }

    public function flush()
    {
        $this->_em->flush();
    }
}