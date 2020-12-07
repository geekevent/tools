<?php

declare(strict_types=1);

namespace App\Repository\Account;

use App\Entity\Account\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class AccountRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function loadUserByUsername(string $username)
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $expr = $queryBuilder->expr();

        return $queryBuilder
            ->where('o.login = :username')
            ->andWhere('o.valid = '.$expr->literal(true))
            ->andWhere('(o.endDate > CURRENT_TIMESTAMP() or o.endDate is null)')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
