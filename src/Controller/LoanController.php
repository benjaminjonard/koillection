<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Loan;
use App\Repository\LoanRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoanController extends AbstractController
{
    #[Route(
        path: ['en' => '/loans', 'fr' => '/prets'],
        name: 'app_loan_index',
        methods: ['GET']
    )]
    public function index(LoanRepository $loanRepository): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['loans']);

        return $this->render('App/Loan/index.html.twig', [
            'loans' => $loanRepository->findLent(),
            'returnedItems' => $loanRepository->findReturned(),
        ]);
    }

    #[Route(
        path: ['en' => '/loans/{id}/delete', 'fr' => '/prets/{id}/supprimer'],
        name: 'app_loan_delete',
        requirements: ['id' => '%uuid_regex%'],
        methods: ['POST']
    )]
    public function delete(Request $request, Loan $loan, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['loans']);

        $form = $this->createDeleteForm('app_loan_delete', $loan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($loan);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.loan_canceled', ['%item%' => '&nbsp;<strong>'.$loan->getItem()->getName().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_loan_index');
    }

    #[Route(
        path: ['en' => '/loans/{id}/returned', 'fr' => '/prets/{id}/rendu'],
        name: 'app_loan_returned',
        requirements: ['id' => '%uuid_regex%'],
        methods: ['GET']
    )]
    #[Entity('loan', expr: 'repository.findByIdWithItem(id)', class: Loan::class)]
    public function returned(Loan $loan, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['loans']);

        $loan->setReturnedAt(new \DateTime());
        $managerRegistry->getManager()->flush();
        $this->addFlash('notice', $translator->trans('message.item_returned', ['%item%' => '&nbsp;<strong>'.$loan->getItem()->getName().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_loan_index');
    }
}
