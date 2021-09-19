<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin', name: 'main_')]
class MainController extends AbstractController
{
    #[Route(path: '', name: 'home')]
    public function home(Request $request)
    {
        return $this->render('Home.html.twig');
    }
}