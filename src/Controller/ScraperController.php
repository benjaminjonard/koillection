<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Scraper;
use App\Form\Type\Entity\ScraperType;
use App\Form\Type\Model\ScrapingType;
use App\Model\Scraping;
use App\Repository\ScraperRepository;
use App\Service\Scraper\HtmlScraper;
use App\Service\Scraper\JsonScraper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ScraperController extends AbstractController
{
    #[Route(path: '/scrapers/scrap', name: 'app_scraper_scrap', methods: ['POST'])]
    public function scrap(Request $request, JsonScraper $jsonApiScraper, HtmlScraper $htmlScraper): JsonResponse
    {
        $scraping = new Scraping();
        $form = $this->createForm(ScrapingType::class, $scraping);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                return $this->json($htmlScraper->scrap($scraping->getScraper(), $scraping->getUrl(), $scraping->getEntity()));
            } catch (\Exception $e) {
                return $this->json($e->getMessage(), 400);
            }
        }

        return $this->json([]);
    }

    #[Route(path: '/scrapers', name: 'app_scraper_index', methods: ['GET'])]
    public function index(ScraperRepository $scraperRepository): Response
    {
        $scrapers = $scraperRepository->findBy([], ['name' => Criteria::ASC]);

        return $this->render('App/Scraper/index.html.twig', [
            'scrapers' => $scrapers,
        ]);
    }

    #[Route(path: '/scrapers/add', name: 'app_scraper_add', methods: ['GET', 'POST'])]
    public function add(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $scraper = new Scraper();

        $form = $this->createForm(ScraperType::class, $scraper);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($scraper);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.scraper_added', ['scraper' => $scraper->getName()]));

            return $this->redirectToRoute('app_scraper_show', ['id' => $scraper->getId()]);
        }

        return $this->render('App/Scraper/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: '/scrapers/{id}/edit', name: 'app_scraper_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Scraper $scraper, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(ScraperType::class, $scraper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.scraper_edited', ['scraper' => $scraper->getName()]));

            return $this->redirectToRoute('app_scraper_show', ['id' => $scraper->getId()]);
        }

        return $this->render('App/Scraper/edit.html.twig', [
            'form' => $form,
            'scraper' => $scraper,
        ]);
    }

    #[Route(path: '/scrapers/{id}/delete', name: 'app_scraper_delete', methods: ['POST'])]
    public function delete(Request $request, Scraper $scraper, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createDeleteForm('app_scraper_delete', $scraper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($scraper);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.scraper_deleted', ['scraper' => $scraper->getName()]));
        }

        return $this->redirectToRoute('app_scraper_index');
    }

    #[Route(path: '/scrapers/{id}', name: 'app_scraper_show', methods: ['GET'])]
    public function show(Scraper $scraper): Response
    {
        return $this->render('App/Scraper/show.html.twig', [
            'scraper' => $scraper,
        ]);
    }
}
