<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Account\Module;
use App\Service\ModuleCreator;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModuleCreatorTest extends KernelTestCase
{
    private static EntityManagerInterface $entityManager;

    private Registry $doctrine;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        self::$entityManager = self::getEntityManager();
    }

    private static function getEntityManager(): EntityManagerInterface
    {
        $entityManger = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        if (!$entityManger instanceof EntityManagerInterface) {
            throw new \InvalidArgumentException('invalid service');
        }

        return $entityManger;
    }

    private function createDatabase(): void
    {
        $managers = $this->doctrine->getManagers();
        foreach ($managers as $manager) {
            if ($manager instanceof EntityManagerInterface) {
                $schemaTool = new SchemaTool($manager);
                $schemaTool->dropDatabase();
                $schemaTool->createSchema($manager->getMetadataFactory()->getAllMetadata());
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->doctrine = $this->getDoctrine();

        $this->createDatabase();
    }

    private function getDoctrine(): Registry
    {
        $registry = self::$kernel->getContainer()->get('doctrine');
        if (!$registry instanceof Registry) {
            throw new \InvalidArgumentException('invalid service');
        }

        return $registry;
    }

    protected function tearDown(): void
    {
    }

    public function testRun(): void
    {
        $creator = new ModuleCreator(self::$entityManager);
        self::assertEquals(0, $creator->run());
    }

    public function testInsert(): void
    {
        $creator = new ModuleCreator(self::$entityManager);
        $creator->run();

        $repository = self::$entityManager->getRepository(Module::class);
        foreach (Module::MODULE as $identifier => $name) {
            $module = $repository->findOneBy(['identifier' => $identifier]);
            self::assertNotNull($module);
            self::assertEquals($name, $module->getName());
        }
    }
}
