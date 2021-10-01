<?php


namespace App\Controller;

use App\Entity\Entry;
use App\Entity\User;
use App\Repository\EntryRepository;
use App\Repository\SpaceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
#[Route(path: '/admin/entry', name: 'entry_')]
class EntryController extends AbstractController
{
    private const ROLE = 'ROLE_STAFF';

    #[Route(path: '/add', name: 'add', methods: Request::METHOD_POST)]
    #[IsGranted(self::ROLE)]
    public function add(Request $request, SpaceRepository $spaceRepository, EntryRepository $entryRepository): Response
    {
        $entry = new Entry();
        $content = json_decode($request->getContent(), true);
        $space = $spaceRepository->find($content['space']);

        if (null === $space) {
            return new JsonResponse([]);
        }

        $entry->space = $space;
        $entry->value = ((int) $content['value']);
        $entry->time = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $entry->user = $this->getUser();

        $entryRepository->persist($entry);
        $entryRepository->flush();

        return new JsonResponse($entryRepository->getGauge($space));
    }
}
