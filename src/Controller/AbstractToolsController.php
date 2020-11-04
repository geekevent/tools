<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AbstractEntity;
use App\Service\MenuBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

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
    protected function findBy(string $className, array $criteria = [], ?array $order = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->getDoctrine()->getRepository($className)->findBy($criteria, $order, $limit, $offset);
    }

    /**
     * @param class-string<AbstractEntity> $className
     */
    protected function findOne(string $className, int $id): ?object
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

    /**
     * @param array<string, mixed> $parameters
     */
    protected function render(string $view, array $parameters = [], Response $response = null, ?Request $request = null): Response
    {
        if ($request) {
            /** @var Router $router */
            $router = $this->get('router');
            $routeName = $request->attributes->get('_route');
            $route = $router->getRouteCollection()->get($routeName);

            $menuBuilder = new MenuBuilder($router, $route);
            $menuBuilder->build();
            $parameters['menu'] = $menuBuilder->menu;
        }

        return parent::render($view, $parameters, $response);
    }
}
