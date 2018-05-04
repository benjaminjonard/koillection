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
 *
 * @Route("/statistics")
 */
class StatisticsController extends AbstractController
{
    /**
     * @Route("", name="app_statistics_index")
     * @Method({"GET"})
     *
     * @param TreeBuilder $treeBuilder
     * @param CalendarBuilder $calendarBuilder
     * @param ChartBuilder $chartBuilder
     * @return Response
     */
    public function index(TreeBuilder $treeBuilder, CalendarBuilder $calendarBuilder, ChartBuilder $chartBuilder) : Response
    {
        $calendar = array_reverse($calendarBuilder->buildItemCalendar($this->getUser()), true);

        return $this->render('App/Statistics/index.html.twig', [
            'counters' => $this->getDoctrine()->getRepository(User::class)->getCounters($this->getUser()),
            'calendarYears' => array_keys($calendar),
            'calendarJson' => json_encode($calendar),
            'treeJson' => json_encode($treeBuilder->buildCollectionTree()),
            'hoursChartJson' => json_encode($chartBuilder->buildActivityByHour($this->getUser())),
            'monthsChartJson' => json_encode($chartBuilder->buildActivityByMonth($this->getUser())),
            'monthDaysChartJson' => json_encode($chartBuilder->buildActivityByMonthDay($this->getUser())),
            'weekDaysChartJson' => json_encode($chartBuilder->buildActivityByWeekDay($this->getUser())),
        ]);
    }
}
