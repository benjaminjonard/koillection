<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\Security\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 * @package App\Controller
 *
 * @Route("")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="app_security_front")
     * @Method({"GET", "POST"})
     *
     * @return Response
     */
    public function front() : Response
    {
        if (0 === $this->getDoctrine()->getRepository(User::class)->countAll()) {
            return $this->redirectToRoute('app_security_first_connection');
        }

        if ($this->getUser() instanceof User) {
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('App/Security/front.html.twig');
    }

    /**
     * @Route("/login", name="app_security_login")
     * @Method({"GET", "POST"})
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
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
     * @Route("/first-connection", name="app_security_first_connection")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     * @return Response
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

        return $this->render('App/Security/first-connection.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
