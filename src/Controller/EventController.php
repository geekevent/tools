<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Space;
use App\Form\Event as Forms;
use App\QrCode\QrCodeGenerator;
use App\Repository\EnigmaRepository;
use App\Repository\EventRepository;
use App\Repository\SpaceRepository;
use Dompdf\Dompdf;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Color\Color;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/events', name: 'events_')]
class EventController extends AbstractController
{
    private const ROLE = 'ROLE_ORGA';

    #[Route(path: '', name: 'create')]
    #[IsGranted(self::ROLE)]
    public function events(Request $request, EventRepository $repository): Response
    {
        $event = new Event();
        $form = $this->createForm(Forms\Create::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->persist($event);
            $repository->flush();

            return $this->redirectToRoute('events_create');
        }

        return $this->render('Events/lists.html.twig', [
            'form' => $form->createView(),
            'items' => $repository->findAll(),
        ]);
    }

    #[Route(path: '/{id}', name: 'update', requirements: [
        'id' => '\d+'
    ])]
    #[Entity(Event::class, expr: 'repository.find(id)')]
    #[IsGranted(self::ROLE)]
    public function update(Request $request, EventRepository $repository, Event $event): Response
    {
        $form = $this->createForm(Forms\Create::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->flush();

            return $this->redirectToRoute('events_create');
        }

        return $this->render('Events/lists.html.twig', [
            'form' => $form->createView(),
            'items' => $repository->findAll(),
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'delete', requirements: [
        'id' => '\d+'
    ])]
    #[Entity(Event::class, expr: 'repository.find(id)')]
    #[IsGranted(self::ROLE)]
    public function delete(EventRepository $repository, Event $event): Response
    {
        $repository->remove($event);
        $repository->flush();

        return $this->redirectToRoute('events_create');
    }

    #[Route(path: '/{event}/spaces', name: 'spaces')]
    #[Entity(Event::class, expr: 'repository.find(event)')]
    #[IsGranted(self::ROLE)]
    public function spaces(Request $request, Event $event, SpaceRepository $repository): Response
    {
        $space = new Space();
        $form = $this->createForm(Forms\Spaces\Create::class, $space);
        $space->event = $event;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $repository->persist($space);
            $repository->flush();

            return $this->redirectToRoute('events_spaces', ['event' => $event->id]);
        }

        return $this->render('/Events/spaces.html.twig', [
            'form' => $form->createView(),
            'items' => $repository->findBy(['event' => $event]),
            'event' => $event,
        ]);
    }

    #[Route(path: '/{event}/spaces/{id}', name: 'spaces_update')]
    #[Entity(Event::class, expr: 'repository.find(event)')]
    #[Entity(Space::class, expr: 'repository.find(id)')]
    #[IsGranted(self::ROLE)]
    public function spacesUpdate(Request $request,  SpaceRepository $repository, Event $event, Space $space): Response
    {
        $form = $this->createForm(Forms\Spaces\Create::class, $space);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $repository->flush();

            return $this->redirectToRoute('events_spaces', ['event' => $event->id]);
        }

        return $this->render('/Events/spaces.html.twig', [
            'form' => $form->createView(),
            'items' => $repository->findBy(['event' => $event]),
            'event' => $event,
        ]);
    }

    #[Route(path: '/{event}/spaces/{id}/delete', name: 'spaces_delete')]
    #[Entity(Event::class, expr: 'repository.find(event)')]
    #[Entity(Space::class, expr: 'repository.find(id)')]
    #[IsGranted(self::ROLE)]
    public function spacesDelete(SpaceRepository $repository, Event $event, Space $space): Response
    {
        $repository->remove($space);
        $repository->flush();

        return $this->redirectToRoute('events_spaces', ['event' => $event->id]);
    }

    #[Route(path: '/{id}/qrCode/all', name: 'qrCode_all')]
    #[Entity(Event::class, expr: 'repository.find(id)')]
    public function getAllQrCode(Event $event, QrCodeGenerator $generator, EnigmaRepository $repository): Response
    {
        $qrCodes = [];
        $pdf = new Dompdf();
        $enigmas = $repository->findBy(['event' => $event]);
        foreach ($enigmas as $enigma) {
            $qrCode =  $generator->generate($enigma);
            $qrCodes[] = [
                'enigma' => $enigma,
                'qrCode' => $qrCode
            ];
        }

        $pdf->loadHtml($this->renderView('Enigma/allQrCode.html.twig', [
            'qrCodes' => $qrCodes,
        ]));

        $pdf->render();
        $repository->flush();

        return new Response($pdf->output(), Response::HTTP_CREATED, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}