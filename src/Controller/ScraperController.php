<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Scraper;
use App\Form\Type\Entity\ScraperType;
use App\Form\Type\Model\ScraperImporterType;
use App\Form\Type\Model\ScrapingType;
use App\Http\FileResponse;
use App\Model\ScraperImporter;
use App\Model\Scraping;
use App\Repository\ScraperRepository;
use App\Service\Scraper\HtmlScraper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ScraperController extends AbstractController
{
    #[Route(path: '/scrapers/scrap', name: 'app_scraper_scrap', methods: ['POST'])]
    public function scrap(Request $request, HtmlScraper $htmlScraper): JsonResponse
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $scraping = new Scraping();
        $form = $this->createForm(ScrapingType::class, $scraping);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                return $this->json($htmlScraper->scrap($scraping));
            } catch (\Exception $e) {
                return $this->json($e->getMessage(), 400);
            }
        }

        return $this->json([]);
    }

    #[Route(path: '/scrapers', name: 'app_scraper_index', methods: ['GET'])]
    public function index(ScraperRepository $scraperRepository): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $scrapers = $scraperRepository->findBy([], ['name' => Criteria::ASC]);

        return $this->render('App/Scraper/index.html.twig', [
            'scrapers' => $scrapers,
            'scraperImportForm' => $this->createForm(ScraperImporterType::class, new ScraperImporter())
        ]);
    }

    #[Route(path: '/scrapers/add', name: 'app_scraper_add', methods: ['GET', 'POST'])]
    public function add(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

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

    #[Route(path: '/scrapers/import', name: 'app_scraper_import', methods: ['POST'])]
    public function import(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $scraperImporter = new ScraperImporter();
        $form = $this->createForm(ScraperImporterType::class, $scraperImporter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scraper = $scraperImporter->toScrapper();
            $managerRegistry->getManager()->persist($scraper);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.scraper_imported', ['scraper' => $scraper->getName()]));

            return $this->redirectToRoute('app_scraper_show', ['id' => $scraper->getId()]);
        }

        $this->addFlash('notice', $translator->trans('message.scraper_import_error'));

        return $this->redirectToRoute('app_scraper_index');
    }

    #[Route(path: '/scrapers/{id}/edit', name: 'app_scraper_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Scraper $scraper, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

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
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $form = $this->createDeleteForm('app_scraper_delete', $scraper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($scraper);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.scraper_deleted', ['scraper' => $scraper->getName()]));
        }

        return $this->redirectToRoute('app_scraper_index');
    }

    #[Route(path: '/scrapers/{id}/export', name: 'app_scraper_export', methods: ['GET'])]
    public function export(Scraper $scraper, SluggerInterface $slugger): FileResponse
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $data = [];

        $data["name"] = $scraper->getName();
        $data["namePath"] = $scraper->getNamePath();
        $data["imagePath"] = $scraper->getImagePath();
        $data["dataPaths"] = [];
        foreach ($scraper->getDataPaths() as $dataPath) {
            $data["dataPaths"][] = [
                "name" => $dataPath['name'] ?? null,
                "path" => $dataPath['path'] ?? null,
                "type" => $dataPath['type'] ?? null,
            ];
        }

        $slug = $slugger->slug($scraper->getName())->lower();

        return new FileResponse([json_encode($data)], "scrapper-$slug.json", headers: ['Content-Type' => 'application/json']);
    }

    #[Route(path: '/scrapers/{id}', name: 'app_scraper_show', methods: ['GET'])]
    public function show(Scraper $scraper): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        return $this->render('App/Scraper/show.html.twig', [
            'scraper' => $scraper,
        ]);
    }
}
