<?php

namespace App\Repository;

use App\Entity\CovidAuthorization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CovidAuthorization[] findAll()
 */
class CovidAuthorizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CovidAuthorization::class);
    }

    public function remove(CovidAuthorization $user)
    {
        $this->_em->remove($user);
    }

    public function persist(CovidAuthorization $user)
    {
        $this->_em->persist($user);
    }

    public function flush()
    {
        $this->_em->flush();
    }
}
