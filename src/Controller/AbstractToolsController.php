<?php

namespace App\Controller;

use App\Entity\AbstractEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractToolsController extends AbstractController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param class-string<AbstractEntity> $className
     * @param array<mixed>                 $criteria
     * @param array<mixed>|null            $order
     *
     * @return object[]
     */
    protected function findBy(string $className, array $criteria, ?array $order = null, ?int $limit = null, ?int $offset = null)
    {
        return $this->getDoctrine()->getRepository($className)->findBy($criteria, $order, $limit, $offset);
    }

    /**
     * @param class-string<AbstractEntity> $className
     *
     * @return object|null
     */
    protected function findOne(string $className, int $id)
    {
        return $this->getDoctrine()->getRepository($className)->find($id);
    }

    protected function save(AbstractEntity $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    protected function update(): void
    {
        $this->entityManager->flush();
    }

    protected function delete(AbstractEntity $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
