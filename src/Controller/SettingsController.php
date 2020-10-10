<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\Model\SettingsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingsController extends AbstractController
{
    /**
     * @Route({
     *     "en": "/settings",
     *     "fr": "/paramètres"
     * }, name="app_settings_index", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function index(Request $request, TranslatorInterface $translator) : Response
    {
        $user = $this->getUser();
        $form = $this->createForm(SettingsType::class, $user);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('notice', $translator->trans('message.settings_updated'));

            return $this->redirectToRoute('app_settings_index');
        }

        return $this->render('App/Settings/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
