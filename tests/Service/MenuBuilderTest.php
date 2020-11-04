<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\MenuBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Router;

class MenuBuilderTest extends KernelTestCase
{
    private static Router $router;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        self::getRouter();
    }

    protected static function getRouter(): void
    {
        $router = self::$kernel->getContainer()->get('router');
        if (!$router instanceof Router) {
            throw new \InvalidArgumentException('get '.\get_class($router).' instead of '.Router::class);
        }

        self::$router = $router;
    }

    protected function tearDown(): void
    {
    }

    public function testBuilder(): void
    {
        $route = self::$router->getRouteCollection()->get('role_list');
        $menuBuilder = new MenuBuilder(self::$router, $route);
        $menuBuilder->build();
        self::assertIsArray($menuBuilder->menu);
    }
}
