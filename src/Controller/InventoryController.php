<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Inventory;
use App\Form\Type\Entity\InventoryType;
use App\Repository\CollectionRepository;
use App\Service\InventoryHandler;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class InventoryController extends AbstractController
{
    #[Route(path: '/inventories/add', name: 'app_inventory_add', methods: ['GET', 'POST'])]
    public function add(Request $request, CollectionRepository $collectionRepository, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $inventory = new Inventory();

        $form = $this->createForm(InventoryType::class, $inventory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($inventory);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.inventory_added', ['inventory' => '&nbsp;<strong>'.$inventory->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_inventory_show', ['id' => $inventory->getId()]);
        }

        return $this->render('App/Inventory/add.html.twig', [
            'form' => $form->createView(),
            'collections' => $collectionRepository->findAll(),
        ]);
    }

    #[Route(path: '/inventories/{id}/delete', name: 'app_inventory_delete', methods: ['POST'])]
    public function delete(Request $request, Inventory $inventory, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createDeleteForm('app_inventory_delete', $inventory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($inventory);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.inventory_deleted', ['inventory' => '&nbsp;<strong>'.$inventory->getName().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_tools_index');
    }

    #[Route(path: '/inventories/{id}/check', name: 'app_inventory_check', methods: ['POST'])]
    public function check(Request $request, Inventory $inventory, InventoryHandler $inventoryHandler, ManagerRegistry $managerRegistry): Response
    {
        $inventoryHandler->setCheckedValue($inventory, (string) $request->request->get('id'), (string) $request->request->get('checked'));
        $managerRegistry->getManager()->flush();

        return new JsonResponse([
            'htmlForNavPills' => $this->render('App/Inventory/_nav_pills.html.twig', ['inventory' => $inventory])->getContent(),
        ]);
    }

    #[Route(path: '/inventories/{id}', name: 'app_inventory_show', methods: ['GET'])]
    public function show(Inventory $inventory): Response
    {
        return $this->render('App/Inventory/show.html.twig', [
            'inventory' => $inventory,
        ]);
    }
}
