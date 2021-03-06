<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        // dump($request);
        $user = new User();

        $formRegistrations = $this->createForm(RegistrationType::class, $user);

        $formRegistrations->handleRequest($request);

        dump($user);

        if($formRegistrations->isSubmitted() && $formRegistrations->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            dump($hash);
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', "Felicitations ! Votre compte a été validé, vous pouvez des à présent vous connecter.");

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [

            'formRegistration' => $formRegistrations->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {   
        // Récupération du message d'erreur en cas de mauvaise connexion
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupération du dernier username (email) saisi par l'internaute en cas d'erreur de connexion
        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'error' => $error,
            'lastEmail' => $lastEmail
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {

    }
}
