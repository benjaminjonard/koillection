<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Graph\CalendarBuilder;
use App\Service\Graph\ChartBuilder;
use App\Service\Graph\TreeBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    #[Route(
        path: ['en' => '/statistics', 'fr' => '/statistiques'],
        name: 'app_statistics_index', methods: ['GET']
    )]
    #[Route(
        path: ['en' => '/user/{username}/statistics', 'fr' => '/utilisateur/{username}/statistiques'],
        name: 'app_shared_statistics_index', methods: ['GET']
    )]
    public function index(TreeBuilder $treeBuilder, CalendarBuilder $calendarBuilder, ChartBuilder $chartBuilder,
                          UserRepository $userRepository, User $user = null
    ) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['statistics']);

        if (!$user instanceof User) {
            $user = $this->getUser();
        }

        $calendar = $calendarBuilder->buildItemCalendar($user);
        ksort($calendar);
        $calendar = \array_reverse($calendar, true);

        return $this->render('App/Statistics/index.html.twig', [
            'counters' => $userRepository->getCounters($user),
            'calendarData' => $calendar,
            'treeJson' => json_encode($treeBuilder->buildCollectionTree()),
            'hoursChartData' => $chartBuilder->buildActivityByHour($user),
            'monthsChartData' => $chartBuilder->buildActivityByMonth($user),
            'monthDaysChartData' => $chartBuilder->buildActivityByMonthDay($user),
            'weekDaysChartData' => $chartBuilder->buildActivityByWeekDay($user),
            'itemsEvolutionData' => $chartBuilder->buildItemEvolution($user),
        ]);
    }
}
