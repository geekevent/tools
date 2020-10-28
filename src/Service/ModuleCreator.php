<?php

namespace App\Service;

use App\Entity\Account\Module;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ModuleCreator
{
    private EntityManagerInterface $entityManager;

    /**
     * @var ObjectRepository<Module>
     */
    private ObjectRepository $moduleRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->getModuleRepository();
    }

    private function getModuleRepository(): void
    {
        $this->moduleRepository = $this->entityManager->getRepository(Module::class);
    }

    public function run(): int
    {
        foreach (Module::MODULE as $identifier => $name) {
            $this->createModule($identifier, $name);
        }

        $this->entityManager->flush();

        return 0;
    }

    private function createModule(string $identifier, string $name): void
    {
        $module = $this->findModule($identifier);
        if (null === $module) {
            $module = new Module();
            $module->setIdentifier($identifier);
            $this->entityManager->persist($module);
        }

        $module->setName($name);
    }

    private function findModule(string $identifier): ?Module
    {
        return $this->moduleRepository->findOneBy(['identifier' => $identifier]);
    }
}
