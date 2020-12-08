<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Security;

class MenuBuilder
{
    /** @var array<string, mixed> */
    public array $menu = [];

    private Router $router;

    private string $currentController;

    private Security $security;

    public function __construct(Router $router, Route $currentRoute, Security $security)
    {
        $this->router = $router;
        $this->currentController = explode('::', $currentRoute->getDefault('_controller'))[0];
        $this->security = $security;
    }

    public function build(): void
    {
        $routes = $this->router->getRouteCollection();
        foreach ($routes as $routeName => $route) {
            if (!$route->getOption('displayed')) {
                continue;
            }
            $require = $route->getOption('require');
            if (null !== $require && (!$this->security->getUser() || !$this->security->isGranted($require))) {
                continue;
            }

            $controller = explode('::', $route->getDefault('_controller'))[0];
            $route->setOption('active', $this->currentController === $controller);
            if ($module = $route->getOption('module')) {
                if (!isset($this->menu[$module['name']])) {
                    $this->menu[$module['name']] = [
                        'active' => false,
                        'title' => $module['title'],
                        'route' => [],
                    ];
                }

                if ($route->getOption('active')) {
                    $this->menu[$module['name']]['active'] = true;
                }
                $this->menu[$module['name']]['route'][$routeName] = $route;

                continue;
            }

            $this->menu[$routeName] = $route;
        }
    }
}
