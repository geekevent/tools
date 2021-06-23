<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account\Role;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class RoleCreator
{
    private EntityManagerInterface $entityManager;

    /**
     * @var ObjectRepository<Role>
     */
    private ObjectRepository $moduleRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->getModuleRepository();
    }

    private function getModuleRepository(): void
    {
        $this->moduleRepository = $this->entityManager->getRepository(Role::class);
    }

    public function run(): int
    {
        foreach (Role::ROLES as $identifier => $name) {
            $this->createRole($identifier, $name);
        }

        $this->entityManager->flush();

        return 0;
    }

    private function createRole(string $identifier, string $name): void
    {
        $module = $this->findRole($identifier);
        if (null === $module) {
            $module = new Role();
            $module
                ->setIdentifier($identifier)
                ->setDeletable(false)
            ;
            $this->entityManager->persist($module);
        }

        $module->setName($name);
    }

    private function findRole(string $identifier): ?Role
    {
        return $this->moduleRepository->findOneBy(['identifier' => $identifier]);
    }
}
