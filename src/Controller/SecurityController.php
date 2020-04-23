<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\Security\UserType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route({
     *     "en": "",
     *     "fr": ""
     * }, name="app_security_login", methods={"GET", "POST"})
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function login(AuthenticationUtils $authenticationUtils) : Response
    {
        if (0 === $this->getDoctrine()->getRepository(User::class)->countAll()) {
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

    /**
     * @Route({
     *     "en": "/first-connection",
     *     "fr": "/premiere-connexion"
     * }, name="app_security_first_connection", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function firstConnectionAction(Request $request, TokenStorageInterface $tokenStorage, SessionInterface $session) : Response
    {
        if (0 < $this->getDoctrine()->getRepository(User::class)->countAll()) {
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('App/Security/first_connection.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
