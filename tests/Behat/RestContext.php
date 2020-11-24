<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\AbstractEntity;
use App\Entity\Account\Account;
use App\Entity\Account\Module;
use App\Entity\Account\Role;
use App\Service\ModuleCreator;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class RestContext implements Context
{
    private KernelBrowser $browser;

    private ContainerInterface $container;

    private ?string $token;

    private ?string $entity;

    private ?Account $account;

    private const JSON_CONTENT_PATH = '/features/payloads';

    private bool $logged = false;

    private bool $init = false;

    public function __construct(KernelBrowser $browser, ContainerInterface $container)
    {
        $this->browser = $browser;
        $this->container = $container;
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario(BeforeScenarioScope $scope): void
    {
        $this->before($scope);
        /** @var Registry $doctrine */
        $doctrine = $this->container->get('doctrine');
        $managers = $doctrine->getManagers();
        foreach ($managers as $manager) {
            if ($manager instanceof EntityManagerInterface) {
                $schemaTool = new SchemaTool($manager);
                $schemaTool->dropDatabase();
                $schemaTool->createSchema($manager->getMetadataFactory()->getAllMetadata());
            }
        }
        $this->logged();
    }

    private function before(BeforeScenarioScope $scope): void
    {
        if ($this->init) {
            return;
        }

        $tags = $scope->getFeature()->getTags();
        foreach ($tags as $tag) {
            $this->getEntity($tag);
            $this->connect($tag);
        }

        $this->init = true;
    }

    /**
     * @When I send a :method request to :path with body:
     */
    public function iSendARequestToWithBody(string $method, string $path, PyStringNode $string): void
    {
        $parameters = (array) json_decode((string) $string, true);
        $parameters[$this->entity]['_token'] = $this->token;

        $this->browser->request(
            $method,
            $path,
            $parameters,
            [],
            [],
            (string) $string
        );
    }

    /**
     * @When I send a :method request to :path
     */
    public function iSendARequestTo(string $method, string $path): void
    {
        $this->browser->request($method, $path);
    }

    /**
     * @When a token for :arg1
     */
    public function aTokenFor(string $arg1): void
    {
        /** @var CsrfTokenManagerInterface $service */
        $service = $this->container->get('security.csrf.token_manager');
        $token = $service->getToken($arg1);
        $this->token = (string) $token;
    }

    /**
     * @Then I found a :arg1 with :arg2 as name
     */
    public function iFoundAWithAsName(string $arg1, string $arg2): void
    {
        /** @var class-string<mixed> $className */
        $className = ClassFactory::getClass($arg1);
        $item = $this->findBy($className, ['name' => $arg2]);

        Assert::assertNotNull($item);
        if (method_exists($item, 'getName')) {
            Assert::assertEquals($arg2, $item->getName());

            return;
        }

        throw new \Exception('unknown function getName for object');
    }

    /**
     * @Then I didn't found a :arg1 with :arg2 as name
     */
    public function iDidntFoundA(string $arg1, string $arg2): void
    {
        /** @var class-string<mixed> $className */
        $className = ClassFactory::getClass($arg1);
        $item = $this->findBy($className, ['name' => $arg2]);

        Assert::assertNull($item);
    }

    /**
     * @When modules are inserted
     */
    public function modulesAreInserted(): void
    {
        $entityManager = $this->getEntityManagerInterface();
        $moduleCreator = new ModuleCreator($entityManager);
        $moduleCreator->run();
    }

    /**
     * @Then the :arg1 with id :arg3 as :arg2 as identifier
     */
    public function theWithIdAsAsIdentifier(string $arg1, string $arg2, string $arg3): void
    {
        /** @var class-string<mixed> $className */
        $className = ClassFactory::getClass($arg1);
        $item = $this->find($className, (int) $arg3);
        if (method_exists($item, 'getIdentifier')) {
            Assert::assertEquals($arg2, $item->getIdentifier());
        }

        throw new \Exception('unknown function getIdentifier for object');
    }

    /**
     * @Then I found in :arg1 with :arg2 as id and with the module with id :arg3
     */
    public function iFoundInWithAsIdTheModuleWithId(string $arg1, int $arg2, int $arg3): void
    {
        /** @var class-string<mixed> $className */
        $className = ClassFactory::getClass($arg1);
        /** @var Role $item */
        $item = $this->find($className, $arg2);

        foreach ($item->getModules() as $module) {
            if ($module->getId() === $arg3) {
                Assert::assertTrue(true);

                return;
            }
        }

        throw new \InvalidArgumentException('no module found in role');
    }

    /**
     * @Then I found a :arg1 with :arg2 as login
     */
    public function iFoundAWithAsLogin(string $arg1, string $arg2): void
    {
        /** @var class-string<mixed> $className */
        $className = ClassFactory::getClass($arg1);
        $entityManager = $this->getEntityManagerInterface();
        /** @var Account|null $account */
        $account = $entityManager->getRepository($className)->findOneBy(['login' => $arg2]);
        Assert::assertNotNull($account);
    }

    /**
     * @When An account created
     */
    public function anAccountCreated(): void
    {
        $account = new Account();
        /** @var Role $role */
        $role = $this->createRole();
        $this->persist($role);
        $this->createAccount($account, $role);
        $this->persist($account, true);

        $this->account = $account;
    }

    /**
     * @Then I found an account with :arg1 as password
     */
    public function iFoundAnAcountWithAsPassword(string $arg1): void
    {
        $account = new Account();
        $entityManager = $this->getEntityManagerInterface();
        /** @var UserPasswordEncoder $encoder */
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($account, $arg1);

        /** @var Account|null $account */
        $account = $entityManager->getRepository(Account::class)->findOneBy(['password' => $password]);
        Assert::assertNotNull($account);
    }

    /**
     * @When I go to the :arg1 route with :arg2 as method and :arg3 as content
     */
    public function iGoToTheResetPasswordRouteWithAsContent(string $arg1, string $arg2, string $arg3): void
    {
        /** @var Router $router */
        $router = $this->container->get('router');
        $path = $router->generate($arg1, [
            'token' => $this->account->getResetToken(),
        ]);

        $parameters = $this->getJsonContent($arg3);

        $parameters[$this->entity]['_token'] = $this->token;

        $this->browser->request(
            $arg2,
            $path,
            $parameters,
            [],
            [],
            (string) json_encode($parameters)
        );
    }

    /**
     * @When I go to the :path path with :arg2 as method and :arg3 as content
     */
    public function iGoToThePathWithAsContent(string $path, string $arg2, string $arg3): void
    {
        $parameters = $this->getJsonContent($arg3);
        $parameters[$this->entity]['_token'] = $this->token;

        $this->browser->request(
            $arg2,
            $path,
            $parameters,
            [],
            [],
            (string) json_encode($parameters)
        );
    }

    /**
     * @return array<mixed>
     */
    private function getJsonContent(string $fileName): array
    {
        $rootDir = $this->container->getParameter('kernel.project_dir');
        $fullyQualifiedFileName = $rootDir.self::JSON_CONTENT_PATH.'/'.$fileName.'.json';
        if (!is_file($fullyQualifiedFileName)) {
            return [];
        }

        $content = file_get_contents($fullyQualifiedFileName);
        if (!$content) {
            return  [];
        }

        $content = json_decode($content, true);

        if (!$content) {
            return [];
        }

        return $content;
    }

    private function getEntity(string $tag): void
    {
        if (!preg_match('/entity::/i', $tag)) {
            return;
        }

        $parts = explode('::', $tag);
        if (!isset($parts[1])) {
            return;
        }

        $this->entity = $parts[1];
    }

    private function connect(string $tag): void
    {
        if ('logged' !== $tag) {
            return;
        }

        $this->logged = true;
    }

    private function logged(): void
    {
        if (!$this->logged) {
            return;
        }
        $clearPassword = '%X12345678';
        $account = new Account();
        /** @var UserPasswordEncoder $encoder */
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($account, $clearPassword);
        $role = $this->createRole();
        $this->createAccount($account, $role);
        $account->setPassword($password);

        $this->persist($role);
        $this->persist($account, true);
        $this->browser->loginUser($account);
    }

    private function createAccount(Account $account, Role $role): void
    {
        $account
            ->setLogin('admin@admin.com')
            ->setRole($role)
            ->setGivenName('Admin')
            ->setFamilyName('admin')
        ;
    }

    private function createRole(): Role
    {
        $role = new Role();
        $role
            ->setIdentifier('ROLE_ADMIN')
            ->setName('ROLE_ADMIN')
        ;

        return $role;
    }

    private function persist(AbstractEntity $object, bool $flush = false): void
    {
        $entityManager = $this->getEntityManagerInterface();
        $entityManager->persist($object);
        if ($flush) {
            $entityManager->flush();
        }
    }

    /**
     * @param class-string<mixed> $class
     */
    private function find(string $class, int $id): ?AbstractEntity
    {
        $entityManager = $this->getEntityManagerInterface();

        return $entityManager->getRepository($class)->find($id);
    }

    private function getEntityManagerInterface(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        return $entityManager;
    }

    /**
     * @param class-string<mixed> $className
     * @param array<mixed, mixed> $criteria
     */
    private function findBy(string $className, array $criteria): ?AbstractEntity
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getEntityManagerInterface();
        /** @var AbstractEntity|null $item */
        $item = $entityManager->getRepository($className)->findOneBy($criteria);

        return $item;
    }
}
