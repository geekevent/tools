<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractToolsController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractToolsController
{
    /**
     * @Route("/admin", name="home")
     */
    public function getHome(Request $request): Response
    {
        return $this->render(
            'home.html.twig',
            [],
            null,
            $request
        );
    }
}
