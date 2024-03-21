<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\Security\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '', name: 'app_security_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository): Response
    {
        if (0 === $userRepository->count([])) {
            return $this->redirectToRoute('app_security_first_connection');
        }

        if ($this->getUser() instanceof User) {
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('App/Security/login.html.twig', [
            'lastUsername' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route(path: '/first-connection', name: 'app_security_first_connection', methods: ['GET', 'POST'])]
    public function firstConnection(
        Request $request,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        UserRepository $userRepository,
        ManagerRegistry $managerRegistry
    ): Response {
        if (0 < $userRepository->count([])) {
            return $this->redirectToRoute('app_homepage');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user
                ->setEnabled(true)
                ->addRole('ROLE_ADMIN')
            ;
            $managerRegistry->getManager()->persist($user);
            $managerRegistry->getManager()->flush();

            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('App/Security/first_connection.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: '/register', name: 'app_security_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        UserRepository $userRepository,
        ManagerRegistry $managerRegistry
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user
                ->setEnabled(true)
                ->addRole('ROLE_USER')
            ;
            $managerRegistry->getManager()->persist($user);
            $managerRegistry->getManager()->flush();

            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('App/Security/first_connection.html.twig', [
            'form' => $form,
        ]);
    }
}
