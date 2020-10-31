<?php

namespace App\Service;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

class MenuBuilder
{
    /** @var array<string, mixed> */
    public array $menu = [];

    private Router $router;

    private string $currentController;

    public function __construct(Router $router, Route $currentRoute)
    {
        $this->router = $router;
        $this->currentController = explode('::', $currentRoute->getDefault('_controller'))[0];
    }

    public function build(): void
    {
        $routes = $this->router->getRouteCollection();
        foreach ($routes as $routeName => $route) {
            if (!$route->getOption('displayed')) {
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
