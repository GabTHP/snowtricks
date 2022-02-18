<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Service\FileUploader;
use Symfony\Component\Mailer\MailerInterface;
use App\Form\FileUploadType;
use Symfony\Component\Mime\Email;

class RegistrationController extends AbstractController
{

    /**
     * @Route("/register", name="app_register")
     */
    public function register(MailerInterface $mailer, Request $request, UserPasswordHasherInterface $userPasswordHasher,  FileUploader $file_uploader, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setRoles(array('ROLE_USER'));
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $file = $form['upload_file']->getData();
            if ($file) {
                $file_name = $file_uploader->upload($file);
                if (null !== $file_name) // for example
                {
                    $directory = $file_uploader->getTargetDirectory();
                    $full_path = $directory . '/' . $file_name;
                } else {
                    // Oups, an error occured !!!
                }
            }

            $user->setAvatar($file_name);
            $token = $this->generateToken();
            $user->setToken($token);
            $user->setValid(False);


            $entityManager->persist($user);
            $entityManager->flush();

            $token = $user->getToken();
            $email = $user->getEmail();


            $email = (new TemplatedEmail())
                ->from('gabriel.bouakira@gmail.com')
                ->to($user->getEmail())
                ->subject('Bienvenue chez Snow Trick !')
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context([
                    'user' => $user
                ]);
            $mailer->send($email);
            $this->addFlash(
                'notice',
                "Un lien de confirmation a été envoyé sur ta boite mail, clique dessus pour activer ton compte !"
            );
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm/{token}/{username}", name="confirm")
     * @param $token
     * @param $username
     * @return Response
     */
    public function confirmAccount($token, $username): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        $tokenExist = $user->getToken();
        if ($token === $tokenExist) {
            $user->setToken(null);
            $user->setValid(true);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_login');
        } else {
            return $this->render('home');
        }
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
