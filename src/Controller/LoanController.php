<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Loan;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoanController extends AbstractController
{
    #[Route(
        path: ['en' => '/loans', 'fr' => '/prets'],
        name: 'app_loan_index', methods: ['GET']
    )]
    public function index() : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['loans']);

        $loanRepository = $this->getDoctrine()->getRepository(Loan::class);
        return $this->render('App/Loan/index.html.twig', [
            'loans' => $loanRepository->findLent(),
            'returnedItems' => $loanRepository->findReturned(),
        ]);
    }

    #[Route(
        path: ['en' => '/loans/{id}/delete', 'fr' => '/prets/{id}/supprimer'],
        name: 'app_loan_delete', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    public function delete(Loan $loan, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['loans']);

        $em = $this->getDoctrine()->getManager();
        $em->remove($loan);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.loan_canceled', ['%item%' => '&nbsp;<strong>'.$loan->getItem()->getName().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_loan_index');
    }

    #[Route(
        path: ['en' => '/loans/{id}/returned', 'fr' => '/prets/{id}/rendu'],
        name: 'app_loan_returned', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    #[Entity(expr: 'repository.findByIdWithItem(id)', class: 'loan')]
    public function returned(Loan $loan, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['loans']);

        $loan->setReturnedAt(new \DateTime());
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('notice', $translator->trans('message.item_returned', ['%item%' => '&nbsp;<strong>'.$loan->getItem()->getName().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_loan_index');
    }
}
