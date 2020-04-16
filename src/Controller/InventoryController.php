<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Inventory;
use App\Form\Type\Entity\InventoryType;
use App\Service\InventoryHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class InventoryController extends AbstractController
{
    /**
     * @Route("/inventories/add", name="app_inventory_add", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
        $inventory = new Inventory();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(InventoryType::class, $inventory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($inventory);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.inventory_added', ['%inventory%' => '&nbsp;<strong>'.$inventory->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_inventory_show', ['id' => $inventory->getId()]);
        }

        return $this->render('App/Inventory/add.html.twig', [
            'form' => $form->createView(),
            'collections' => $this->getDoctrine()->getRepository(Collection::class)->findAll()
        ]);
    }

    /**
     * @Route("/inventories/{id}/delete", name="app_inventory_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Inventory $inventory
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Inventory $inventory, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($inventory);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.inventory_deleted', ['%inventory%' => '&nbsp;<strong>'.$inventory->getName().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_tools_index');
    }

    /**
     * @Route("/inventories/{id}/check", name="app_inventory_check", methods={"POST"})
     *
     * @param Request $request
     * @param Inventory $inventory
     * @return Response
     */
    public function check(Request $request, Inventory $inventory, InventoryHandler $inventoryHandler) : Response
    {
        $inventoryHandler->setCheckedValues($inventory, $request->request->get('items', []));
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'htmlForNavPills' => $this->render('App/Inventory/_partials/_nav_pills.html.twig', ['inventory' => $inventory])->getContent()
        ]);
    }

    /**
     * @Route("/inventories/{id}", name="app_inventory_show", methods={"GET"})
     *
     * @param Inventory $inventory
     * @return Response
     */
    public function show(Inventory $inventory) : Response
    {
        return $this->render('App/Inventory/show.html.twig', [
            'inventory' => $inventory
        ]);
    }
}
