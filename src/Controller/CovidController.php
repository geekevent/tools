<?php

namespace App\Controller;

use App\Entity\CovidAuthorization;
use App\Entity\User;
use App\Form\CovidAuthorization\Create;
use App\Repository\CovidAuthorizationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/covid', name: 'covid_')]
class CovidController extends AbstractController
{
    private const ROLE = 'ROLE_ORGA';

    #[Route(path: '', name: 'add')]
    #[IsGranted(self::ROLE)]
    public function add(Request $request, CovidAuthorizationRepository $covidAuthorizationRepository): Response
    {
        $covidAuthorization = new CovidAuthorization();

        $form = $this->createForm(Create::class, $covidAuthorization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $covidAuthorizationRepository->persist($covidAuthorization);
            $covidAuthorizationRepository->flush();

            return $this->redirectToRoute('covid_add');
        }

        return $this->render('CovidAuthorization/lists.html.twig', [
            'form'  => $form->createView(),
            'items' => $covidAuthorizationRepository->findAll(),
        ]);
    }

    #[Route(path: '/{id}', name: 'update')]
    #[IsGranted(self::ROLE)]
    public function update(Request $request, CovidAuthorization $covidAuthorization, CovidAuthorizationRepository $covidAuthorizationRepository): Response
    {
        $form = $this->createForm(Create::class, $covidAuthorization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $covidAuthorizationRepository->flush();

            return $this->redirectToRoute('covid_add');
        }

        return $this->render('CovidAuthorization/lists.html.twig', [
            'form'  => $form->createView(),
            'items' => $covidAuthorizationRepository->findAll(),
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'delete')]
    #[IsGranted(self::ROLE)]
    public function delete(CovidAuthorization $covidAuthorization, CovidAuthorizationRepository $covidAuthorizationRepository): Response
    {
        $covidAuthorizationRepository->remove($covidAuthorization);
        $covidAuthorizationRepository->flush();

        return $this->redirectToRoute('covid_add');
    }
}
