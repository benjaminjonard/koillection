<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Graph\CalendarBuilder;
use App\Service\Graph\ChartBuilder;
use App\Service\Graph\TreeBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StatisticsController
 *
 * @package App\Controller
 */
class StatisticsController extends AbstractController
{
    /**
     * @Route("/statistics", name="app_statistics_index")
     * @Route("/user/{username}/statistics", name="app_user_statistics_index")
     * @Route("/preview/statistics", name="app_preview_statistics_index")
     * @Method({"GET"})
     *
     * @param TreeBuilder $treeBuilder
     * @param CalendarBuilder $calendarBuilder
     * @param ChartBuilder $chartBuilder
     * @return Response
     */
    public function index(TreeBuilder $treeBuilder, CalendarBuilder $calendarBuilder, ChartBuilder $chartBuilder) : Response
    {
        $calendar = $calendarBuilder->buildItemCalendar($this->getUser());
        ksort($calendar);
        $calendar = array_reverse($calendar, true);

        return $this->render('App/Statistics/index.html.twig', [
            'counters' => $this->getDoctrine()->getRepository(User::class)->getCounters($this->getUser()),
            'calendarData' => $calendar,
            'treeJson' => json_encode($treeBuilder->buildCollectionTree()),
            'hoursChartData' => $chartBuilder->buildActivityByHour($this->getUser()),
            'monthsChartData' => $chartBuilder->buildActivityByMonth($this->getUser()),
            'monthDaysChartData' => $chartBuilder->buildActivityByMonthDay($this->getUser()),
            'weekDaysChartData' => $chartBuilder->buildActivityByWeekDay($this->getUser()),
            'itemsEvolutionData' => $chartBuilder->buildItemEvolution($this->getUser()),
        ]);
    }
}
