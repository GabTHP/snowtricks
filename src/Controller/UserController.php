<?php

namespace App\Controller;

use App\Form\ResetPassType;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/password-request", name="password_request")
     */

    public function password_request(MailerInterface $mailer, Request $request, UserRepository $users): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ResetPassType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData();
            $user = $users->findOneBy(array('username' => $donnees['username']));
            if ($user === null) {
                $this->addFlash('danger', "Il n'existe pas de compte avec ce pseudo");
                return $this->redirectToRoute('app_login');
            }
            $token = $this->generateToken();
            $user->setToken($token);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $token = $user->getToken();
            $email = $user->getEmail();


            $email = (new TemplatedEmail())
                ->from('gabriel.bouakira@gmail.com')
                ->to($user->getEmail())
                ->subject('SnowTrick - Nouveau not de passe !')
                ->htmlTemplate('security/new_password_email.html.twig')
                ->context([
                    'user' => $user
                ]);
            $mailer->send($email);
            $this->addFlash(
                'notice',
                "Un lien de reinitiliation a été envoyé sur ta boite mail, clique dessus pour modifer ton mot de passe !"
            );
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/password-request.twig', [
            'passwordForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
