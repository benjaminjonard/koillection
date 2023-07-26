<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Scrapper;
use App\Form\Type\Entity\ScrapperType;
use App\Form\Type\Model\ScrappingType;
use App\Model\Scrapping;
use App\Repository\ScrapperRepository;
use App\Service\Scrapper\HtmlScrapper;
use App\Service\Scrapper\JsonScrapper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ScrapperController extends AbstractController
{
    #[Route(path: '/scrappers/scrap', name: 'app_scrapper_scrap', methods: ['POST'])]
    public function scrap(Request $request, JsonScrapper $jsonApiScrapper, HtmlScrapper $htmlScrapper): JsonResponse
    {
        $scrapping = new Scrapping();
        $form = $this->createForm(ScrappingType::class, $scrapping);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                return $this->json($htmlScrapper->scrap($scrapping->getScrapper(), $scrapping->getUrl(), $scrapping->getEntity()));
            } catch (\Exception $e) {
                return $this->json($e->getMessage(), 400);
            }
        }

        return $this->json([]);
    }

    #[Route(path: '/scrappers', name: 'app_scrapper_index', methods: ['GET'])]
    public function index(ScrapperRepository $scrapperRepository): Response
    {
        $scrappers = $scrapperRepository->findBy([], ['name' => Criteria::ASC]);

        return $this->render('App/Scrapper/index.html.twig', [
            'scrappers' => $scrappers,
        ]);
    }

    #[Route(path: '/scrappers/add', name: 'app_scrapper_add', methods: ['GET', 'POST'])]
    public function add(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $scrapper = new Scrapper();

        $form = $this->createForm(ScrapperType::class, $scrapper);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($scrapper);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.scrapper_added', ['scrapper' => $scrapper->getName()]));

            return $this->redirectToRoute('app_scrapper_show', ['id' => $scrapper->getId()]);
        }

        return $this->render('App/Scrapper/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: '/scrappers/{id}/edit', name: 'app_scrapper_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Scrapper $scrapper, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(ScrapperType::class, $scrapper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.scrapper_edited', ['scrapper' => $scrapper->getName()]));

            return $this->redirectToRoute('app_scrapper_show', ['id' => $scrapper->getId()]);
        }

        return $this->render('App/Scrapper/edit.html.twig', [
            'form' => $form,
            'scrapper' => $scrapper,
        ]);
    }

    #[Route(path: '/scrappers/{id}/delete', name: 'app_scrapper_delete', methods: ['POST'])]
    public function delete(Request $request, Scrapper $scrapper, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createDeleteForm('app_scrapper_delete', $scrapper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($scrapper);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.scrapper_deleted', ['scrapper' => $scrapper->getName()]));
        }

        return $this->redirectToRoute('app_scrapper_index');
    }

    #[Route(path: '/scrappers/{id}', name: 'app_scrapper_show', methods: ['GET'])]
    public function show(Scrapper $scrapper): Response
    {
        return $this->render('App/Scrapper/show.html.twig', [
            'scrapper' => $scrapper,
        ]);
    }
}
