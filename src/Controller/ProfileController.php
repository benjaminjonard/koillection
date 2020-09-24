<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Item;
use App\Form\Type\Model\FeaturesType;
use App\Form\Type\Model\ProfileType;
use App\Form\Type\Model\SettingsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route({
     *     "en": "/profile",
     *     "fr": "/profil"
     * }, name="app_profile_index", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function index(Request $request, TranslatorInterface $translator) : Response
    {
        $user = $this->getUser();
        $formSettings = $this->createForm(SettingsType::class, $user);
        $formSettings->handleRequest($request);

        $formProfile = $this->createForm(ProfileType::class, $user);
        $formProfile->handleRequest($request);

        $formFeatures = $this->createForm(FeaturesType::class, $user);
        $formFeatures->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($formSettings->isSubmitted() && $formSettings->isValid()) {
            $em->flush();
            $this->addFlash('notice', $translator->trans('message.settings_updated'));

            return $this->redirectToRoute('app_profile_index');
        }

        if ($formProfile->isSubmitted() && $formProfile->isValid()) {
            $em->flush();
            $this->addFlash('notice', $translator->trans('message.profile_updated'));

            return $this->redirectToRoute('app_profile_index');
        }

        if ($formFeatures->isSubmitted() && $formFeatures->isValid()) {
            $em->flush();
            $this->addFlash('notice', $translator->trans('message.settings_updated'));

            return $this->redirectToRoute('app_profile_index');
        }

        return $this->render('App/Profile/index.html.twig', [
            'lastCollectionsAdded' => $em->getRepository(Collection::class)->findBy(['owner' => $this->getUser()], ['createdAt' => 'DESC'], 5),
            'lastItemsAdded' => $em->getRepository(Item::class)->findBy(['owner' => $this->getUser()], ['createdAt' => 'DESC'], 5),
            'formSettings' => $formSettings->createView(),
            'formProfile' => $formProfile->createView(),
            'formFeatures' => $formFeatures->createView(),
        ]);
    }
}
