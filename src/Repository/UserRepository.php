<?php


namespace App\Repository;


use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function remove(User $user)
    {
        $this->_em->remove($user);
    }

    public function persist(User $user)
    {
        $this->_em->persist($user);
    }

    public function flush()
    {
        $this->_em->flush();
    }
}