<?php


namespace App\Repository;


use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer[] findAll()
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public function remove(Answer $user)
    {
        $this->_em->remove($user);
    }

    public function persist(Answer $user)
    {
        $this->_em->persist($user);
    }

    public function flush()
    {
        $this->_em->flush();
    }
}