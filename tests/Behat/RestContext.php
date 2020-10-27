<?php

namespace App\Tests\Behat;

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
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class RestContext implements Context
{
    private KernelBrowser $browser;

    private ContainerInterface $container;

    private ?string $token;

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
        $parameters['role']['_token'] = $this->token;
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
        $item = $entityManager->getRepository($className)->findOneBy(['name' => $arg2]);

        Assert::assertNull($item);
    }
}
