<?php


namespace App\Repository;


use App\Entity\Answer;
use App\Entity\VisitorAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VisitorAnswer[] findAll()
 * @method VisitorAnswer|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method VisitorAnswer[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 */
class VisitorAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisitorAnswer::class);
    }

    public function remove(VisitorAnswer $user)
    {
        $this->_em->remove($user);
    }

    public function persist(VisitorAnswer $user)
    {
        $this->_em->persist($user);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function getCode(string $code): string
    {
        do {
            $visitorAnswer = $this->findOneBy(['code' => $code]);
            if (null !== $visitorAnswer) {
                $code = bin2hex(random_bytes(2));
            }
        } while (null !== $visitorAnswer);

        return $code;
    }
}