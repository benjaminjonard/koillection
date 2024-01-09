<?php

declare(strict_types=1);

namespace App\Controller\Scraper;

use App\Controller\AbstractController;
use App\Entity\Scraper;
use App\Enum\ScraperTypeEnum;
use App\Form\Type\Entity\ScraperType;
use App\Form\Type\Model\CollectionScraperImporterType;
use App\Form\Type\Model\ScrapingCollectionType;
use App\Http\FileResponse;
use App\Model\Scraper\CollectionScraperImporter;
use App\Model\ScrapingCollection;
use App\Repository\ScraperRepository;
use App\Service\Scraper\HtmlCollectionScraper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CollectionScraperController extends AbstractController
{
    #[Route(path: '/scrapers/collection-scrapers/scrap', name: 'app_scraper_collection_scrap', methods: ['POST'])]
    public function scrap(Request $request, HtmlCollectionScraper $htmlScraper): JsonResponse
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $scraping = new ScrapingCollection();
        $form = $this->createForm(ScrapingCollectionType::class, $scraping);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                return $this->json($htmlScraper->scrap($scraping));
            } catch (\Exception $e) {
                return $this->json($e->getMessage(), 400);
            }
        }

        $formHtml = $this->render('App/Scraper/_scraping_form.html.twig', [
           'scrapingForm' => $form
        ])->getContent();

        return $this->json(['form' => $formHtml], 400);
    }

    #[Route(path: '/scrapers/collection-scrapers/{id}/data-paths-checkboxes', name: 'app_scraper_collection_data_paths_checkboxes', methods: ['GET'])]
    public function getDataPathsCheckboxes(Scraper $scraper): JsonResponse
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $scraping = new ScrapingCollection();
        $scraping->setScraper($scraper);

        $form = $this->createForm(ScrapingCollectionType::class, $scraping, ['choices' => $scraper->getDataPaths()]);

        $html = $this->render('App/Scraper/_data_path_checkboxes.html.twig', [
            'form' => $form
        ])->getContent();

        return new JsonResponse(['html' => $html]);
    }

    #[Route(path: '/scrapers/collection-scrapers', name: 'app_scraper_collection_index', methods: ['GET'])]
    public function index(ScraperRepository $scraperRepository): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $scrapers = $scraperRepository->findBy(['type' => ScraperTypeEnum::TYPE_COLLECTION], ['name' => Criteria::ASC]);

        return $this->render('App/Scraper/collection/index.html.twig', [
            'scrapers' => $scrapers,
            'scraperImportForm' => $this->createForm(CollectionScraperImporterType::class, new CollectionScraperImporter())
        ]);
    }

    #[Route(path: '/scrapers/collection-scrapers/add', name: 'app_scraper_collection_add', methods: ['GET', 'POST'])]
    public function add(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $scraper = new Scraper();
        $scraper->setType(ScraperTypeEnum::TYPE_COLLECTION);

        $form = $this->createForm(ScraperType::class, $scraper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($scraper);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.scraper_added', ['scraper' => $scraper->getName()]));

            return $this->redirectToRoute('app_scraper_collection_show', ['id' => $scraper->getId()]);
        }

        return $this->render('App/Scraper/collection/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: '/scrapers/collection-scrapers/import', name: 'app_scraper_collection_import', methods: ['POST'])]
    public function import(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry, ValidatorInterface $validator): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $scraperImporter = new CollectionScraperImporter();
        $form = $this->createForm(CollectionScraperImporterType::class, $scraperImporter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scraper = $scraperImporter->toScrapper();
            if ($validator->validate($scraper)->count() === 0) {
                $managerRegistry->getManager()->persist($scraper);
                $managerRegistry->getManager()->flush();

                $this->addFlash('notice', $translator->trans('message.scraper_imported', ['scraper' => $scraper->getName()]));

                return $this->redirectToRoute('app_scraper_collection_show', ['id' => $scraper->getId()]);
            }
        }

        $this->addFlash('error', $translator->trans('message.scraper_import_error'));

        return $this->redirectToRoute('app_scraper_collection_index');
    }

    #[Route(path: '/scrapers/collection-scrapers/{id}/edit', name: 'app_scraper_collection_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Scraper $scraper, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $form = $this->createForm(ScraperType::class, $scraper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.scraper_edited', ['scraper' => $scraper->getName()]));

            return $this->redirectToRoute('app_scraper_collection_show', ['id' => $scraper->getId()]);
        }

        return $this->render('App/Scraper/collection/edit.html.twig', [
            'form' => $form,
            'scraper' => $scraper,
        ]);
    }

    #[Route(path: '/scrapers/collection-scrapers/{id}/delete', name: 'app_scraper_collection_delete', methods: ['POST'])]
    public function delete(Request $request, Scraper $scraper, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $form = $this->createDeleteForm('app_scraper_collection_delete', $scraper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($scraper);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.scraper_deleted', ['scraper' => $scraper->getName()]));
        }

        return $this->redirectToRoute('app_scraper_collection_index');
    }

    #[Route(path: '/scrapers/collection-scrapers/{id}/export', name: 'app_scraper_collection_export', methods: ['GET'])]
    public function export(Scraper $scraper, SluggerInterface $slugger): FileResponse
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        $data = [];

        $data['name'] = $scraper->getName();
        $data['namePath'] = $scraper->getNamePath();
        $data['imagePath'] = $scraper->getImagePath();
        $data['urlPattern'] = $scraper->getUrlPattern();
        $data['dataPaths'] = [];
        foreach ($scraper->getDataPaths() as $key => $dataPath) {
            $data['dataPaths'][] = [
                'name' => $dataPath->getName() ?? null,
                'path' => $dataPath->getPath() ?? null,
                'type' => $dataPath->getType() ?? null,
                'position' => $dataPath->getPosition() ?? $key,
            ];
        }

        $slug = $slugger->slug($scraper->getName())->lower();

        return new FileResponse([json_encode($data)], "collection-scrapper-{$slug}.json", headers: ['Content-Type' => 'application/json']);
    }

    #[Route(path: '/scrapers/collection-scrapers/{id}', name: 'app_scraper_collection_show', methods: ['GET'])]
    public function show(Scraper $scraper): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['scraping']);

        return $this->render('App/Scraper/collection/show.html.twig', [
            'scraper' => $scraper,
        ]);
    }
}
