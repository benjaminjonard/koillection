<?php

namespace App\Controller;

use App\Enum\CurrencyEnum;
use App\Enum\LocaleEnum;
use App\Enum\ThemeEnum;
use App\Enum\VisibilityEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SettingsController
 *
 * @package App\Controller
 *
 * @Route("/settings")
 */
class SettingsController extends AbstractController
{
    /**
     * @Route("", name="app_settings_index", methods={"GET"})
     *
     * @param ThemeEnum $themeEnum
     * @param LocaleEnum $localeEnum
     * @param CurrencyEnum $currencyEnum
     * @return Response
     */
    public function index(ThemeEnum $themeEnum, LocaleEnum $localeEnum, CurrencyEnum $currencyEnum) : Response
    {
        return $this->render('App/Settings/index.html.twig', [
            'themes' => $themeEnum->getThemeLabels(),
            'locales' => $localeEnum->getLocaleLabels(),
            'currencies' => $currencyEnum->getCurrencyLabels(),
            'visibilities' => VisibilityEnum::VISIBILITIES_TRANS_KEYS,
        ]);
    }

    /**
     * @Route("/set-locale/{locale}", name="app_settings_set_locale", methods={"GET"})
     *
     * @param Request $request
     * @param string $locale
     * @return Response
     */
    public function setLocale(Request $request, string $locale) : Response
    {
        $user = $this->getUser();
        $user->setLocale($locale);
        $this->getDoctrine()->getManager()->flush();
        $request->getSession()->set('_locale', $locale);

        return $this->redirectToRoute('app_settings_index', ['_locale' => $locale]);
    }

    /**
     * @Route("/set-theme/{theme}", name="app_settings_set_theme", methods={"POST"})
     *
     * @param string $theme
     * @return JsonResponse
     */
    public function setTheme(string $theme, Packages $packages) : JsonResponse
    {
        try {
            $user = $this->getUser();
            $user->setTheme($theme);
            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse(['theme' => $packages->getUrl('build/css/themes/'.$user->getTheme().'.css')]);
        } catch (\Exception $e) {
            return new JsonResponse(false, 500);
        }
    }

    /**
     * @Route("/set-visibility/{visibility}", name="app_settings_set_visibility", methods={"POST"})
     *
     * @param string $visibility
     * @return JsonResponse
     */
    public function setVisibility(string $visibility) : JsonResponse
    {
        try {
            $user = $this->getUser();
            $user->setVisibility($visibility);
            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse(['visibility' => $user->getVisibility()]);
        } catch (\Exception $e) {
            return new JsonResponse(false, 500);
        }
    }

    /**
     * @Route("/set-currency/{currency}", name="app_settings_set_currency", methods={"POST"})
     *
     * @param string $currency
     * @return JsonResponse
     */
    public function setCurrency(string $currency) : JsonResponse
    {
        try {
            $user = $this->getUser();
            $user->setCurrency($currency);
            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse(['currency' => $user->getCurrency()]);
        } catch (\Exception $e) {
            return new JsonResponse(false, 500);
        }
    }
}
