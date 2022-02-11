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


            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                ->from('gabriel.bouakira@gmail.com')
                ->to($user->getEmail())
                ->subject('Welcome to the Space Bar!')
                ->htmlTemplate('registration/confirmation_email.html.twig');
            $mailer->send($email);

            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
