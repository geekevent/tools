<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\AbstractEntity;
use App\Entity\Account\Account;
use App\Entity\Account\Module;
use App\Entity\Account\Role;
use App\Entity\IdentifiedEntity;
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
        /** @var EntityManager $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $item = $entityManager->getRepository($className)->findOneBy(['name' => $arg2]);

        Assert::assertNotNull($item);
        Assert::assertEquals($arg2, $item->getName());
    }

    /**
     * @Then I didn't found a :arg1 with :arg2 as name
     */
    public function iDidntFoundA(string $arg1, string $arg2): void
    {
        /** @var class-string<mixed> $className */
        $className = ClassFactory::getClass($arg1);
        /** @var EntityManager $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        /** @var AbstractEntity|null $item */
        $item = $entityManager->getRepository($className)->findOneBy(['name' => $arg2]);

        Assert::assertNull($item);
    }

    /**
     * @When modules are inserted
     */
    public function modulesAreInserted(): void
    {
        /** @var EntityManager $entityManager */
        $entityManager = $entityManager = $this->container->get('doctrine.orm.entity_manager');
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
        /** @var EntityManager $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        /** @var IdentifiedEntity $item */
        $item = $entityManager->getRepository($className)->findOneBy(['id' => $arg3]);

        Assert::assertEquals($arg2, $item->getIdentifier());
    }

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $event): void
    {
        $tags = $event->getFeature()->getTags();
        foreach ($tags as $tag) {
            if (!preg_match('/entity::/i', $tag)) {
                continue;
            }

            $parts = explode('::', $tag);
            if (!isset($parts[1])) {
                continue;
            }

            $this->entity = $parts[1];
        }
    }

    /**
     * @Then I found in :arg1 with :arg2 as id and with the module with id :arg3
     */
    public function iFoundInWithAsIdTheModuleWithId(string $arg1, int $arg2, int $arg3): void
    {
        /** @var class-string<mixed> $className */
        $className = ClassFactory::getClass($arg1);
        /** @var EntityManager $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        /** @var Role $item */
        $item = $entityManager->getRepository($className)->findOneBy(['id' => $arg2]);
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
        /** @var EntityManager $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
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
        $account
            ->setLogin('test@test.test')
            ->setFamilyName('foo')
            ->setGivenName('bar')
        ;

        /** @var EntityManager $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $entityManager->persist($account);
        $entityManager->flush();
        $this->account = $account;
    }

    /**
     * @Then I found an acount with :arg1 as password
     */
    public function iFoundAnAcountWithAsPassword(string $arg1): void
    {
        $account = new Account();
        /** @var EntityManager $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        /** @var UserPasswordEncoder $encoder */
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($account, $arg1);
        /** @var Account|null $account */
        $account = $entityManager->getRepository(Account::class)->findOneBy(['password' => $password]);
        Assert::assertNotNull($account);
    }

    /**
     * @When I go to the reset password route with :arg1 as content
     */
    public function iGoToTheResetPasswordRouteWithAsContent(string $arg1): void
    {
        /** @var Router $router */
        $router = $this->container->get('router');
        $path = $router->generate('account_reset', [
            'token' => $this->account->getResetToken(),
        ]);

        $parameters = $this->getJsonContent($arg1);

        $parameters[$this->entity]['_token'] = $this->token;

        $this->browser->request(
            Request::METHOD_POST,
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
}
