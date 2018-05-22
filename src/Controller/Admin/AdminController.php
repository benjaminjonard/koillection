<?php

namespace App\Controller\Admin;

use App\Entity\Collection;
use App\Entity\Connection;
use App\Entity\Item;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\PeriodEnum;
use App\Service\DiskUsageCalculator;
use App\Service\Graph\ChartBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AdminController
 *
 * @package App\Controller
 *
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("", name="app_admin_index")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function index(ChartBuilder $chartBuilder) : Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('App/Admin/Admin/index.html.twig', [
            'totalSpaceUsed' => $em->getRepository(User::class)->getTotalSpaceUsed(),
            'freeSpace' => disk_free_space('/'),
            'totalSpace' => disk_total_space('/'),
            'counters' => [
                'users' => $em->getRepository(User::class)->countAll(),
                'collections' => $em->getRepository(Collection::class)->countAll(),
                'items' => $em->getRepository(Item::class)->countAll(),
                'tags' => $em->getRepository(Tag::class)->countAll(),
                'wishlists' => $em->getRepository(Wishlist::class)->countAll(),
                'wishes' => $em->getRepository(Wish::class)->countAll(),
            ]
        ]);
    }

    /**
     * @Route("/analytics", name="app_admin_analytics")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param ChartBuilder $chartBuilder
     * @return Response
     */
    public function analytics(Request $request, ChartBuilder $chartBuilder) : Response
    {
        $period = $request->query->get('period');
        if (!\in_array($period, PeriodEnum::PERIODS, false)) {
            $period = PeriodEnum::PERIOD_TODAY;
        }
        $since = PeriodEnum::getDateSince($period);
        $count = $this->getDoctrine()->getRepository(Connection::class)->countSince($since);

        return $this->render('App/Admin/Admin/analytics.html.twig', [
            'connectionsCounter' => $count,
            'selectedPeriod' => $period,
            'themesUsageJson' => json_encode($chartBuilder->buildThemesUsage()),
            'localesUsageJson' => json_encode($chartBuilder->buildLocalesUsage()),
            'browsersJson' => json_encode($chartBuilder->buildAnalyticsChart($since, $count, 'browser_name')),
            'browsersVersionsJson' => json_encode($chartBuilder->buildAnalyticsChart($since, $count, 'browser_name', 'browser_version')),
            'enginesJson' => json_encode($chartBuilder->buildAnalyticsChart($since, $count, 'engine_name')),
            'engineVersionsJson' => json_encode($chartBuilder->buildAnalyticsChart($since, $count, 'engine_name', 'engine_version')),
            'ossJson' => json_encode($chartBuilder->buildAnalyticsChart($since, $count, 'os_name')),
            'ossVersionsJson' => json_encode($chartBuilder->buildAnalyticsChart($since, $count, 'os_name', 'os_version')),
            'devicesTypesJson' => json_encode($chartBuilder->buildAnalyticsChart($since, $count, 'device_type')),
            'devicesSubtypesJson' => json_encode($chartBuilder->buildAnalyticsChart($since, $count, 'device_type', 'device_subtype')),
            'devicesJson' => json_encode($chartBuilder->buildAnalyticsChart($since, $count, 'device_manufacturer')),
            'devicesModelsJson' => json_encode($chartBuilder->buildAnalyticsChart($since, $count, 'device_manufacturer', 'device_model')),
        ]);
    }
}
