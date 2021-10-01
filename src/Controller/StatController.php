<?php


namespace App\Controller;

use App\Entity\Entry;
use App\Entity\Space;
use App\Entity\User;
use App\Repository\EntryRepository;
use App\Repository\SpaceRepository;
use App\Stats\Stats;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
#[Route(path: '/admin/spaces', name: 'spaces_')]
class StatController extends AbstractController
{
    private const ROLE = 'ROLE_STAFF';

    #[Route(path: '', name: 'all')]
    #[IsGranted(self::ROLE)]
    public function spaces(SpaceRepository $repository): Response
    {
        $count = $repository->countActiveSpace();

        if (0 === $count) {
            return $this->render('Stat/spaces.html.twig', [
                'items' => [],
            ]);
        }

        return $this->render('Stat/spaces.html.twig', [
            'items' => array_map(function (array $data): array {
                return [
                    'space' => $data[0],
                    'gauge' => $data['gauge'],
                ];
            }, $repository->getActiveSpaces()),
        ]);
    }

    #[Route(path: '/{id}', name: 'entry_interface')]
    #[Entity(Space::class, expr: 'repository.find(id)')]
    #[IsGranted(self::ROLE)]
    public function statsEntryInterface(EntryRepository $repository, Space $space): Response
    {
        if (!$space->isActive()) {
            return $this->redirectToRoute('main_home');
        }

        $data = $repository->getGauge($space);

        $gaugeClass = 'text-info';
        if ($space->gaugeMax <= $data['gauge']) {
            $gaugeClass = 'text-danger';
        } elseif (100 >= $space->gaugeMax - $data['gauge']) {
            $gaugeClass = 'text-warning';
        }

        return $this->render('Stat/Entry/interface.html.twig', [
            'back_route' => 'spaces_all',
            'route' => 'entry_add',
            'space' => $space,
            'gauge' => $data['gauge'] ?? 0,
            'gauge_class' => $gaugeClass,
        ]);
    }

    #[Route('/{id}/reset', name: 'entry_reset')]
    #[Entity(Space::class, expr: 'repository.find(id)')]
    #[IsGranted(User::ROLE_ORGA)]
    public function spaceReset(EntryRepository $repository, Space $space): Response
    {
        $data = $repository->getGauge($space);
        $entry = new Entry();
        $entry->value = $data['gauge'] * -1;
        $entry->space = $space;
        $entry->time = new \DateTime();
        $entry->user = $this->getUser();

        $repository->persist($entry);
        $repository->flush();

        return $this->redirectToRoute('spaces_entry_interface', ['id' => $space->id]);
    }

    #[Route(path: '/{id}/graph', name: 'entry_graph')]
    #[Entity(Space::class, expr: 'repository.find(id)')]
    #[IsGranted(User::ROLE_ORGA)]
    public function graph(Request $request, EntryRepository $repository, Space $space, Stats $stats)
    {

        $dates = $repository->getExtremeEntryDateForSpace($space);
        $period = $stats->createDatePeriod($dates);
        $date = $request->query->has('date') ? new \DateTime($request->query->get('date')) : new \DateTime($dates['minDate']);

        $moment = $repository->getGaugeByMoment($space, clone $date);
        return $this->render('Stat/graph.html.twig', [
            'moments' => $stats->prepare($moment),
            'dates' => $period,
            'selectedDate' => $date->format('Y-m-d'),
        ]);
    }
}
