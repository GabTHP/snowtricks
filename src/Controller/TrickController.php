<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Media;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Video;
use App\Form\FormTrickType;
use App\Form\FormMessageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request; // Nous avons besoin d'accéder à la requête pour obtenir le numéro de page
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator
use App\Service\FileUploader;

class TrickController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $tricks = $this->getDoctrine()->getRepository(Trick::class)->findAll();

        return $this->render(
            'trick/index.html.twig',
            [
                'tricks' => $tricks
            ]
        );
    }

    /**
     * @Route("/new-trick", name="new_trick")
     */
    public function newTrick(Request $request,  FileUploader $file_uploader)
    {
        $trick = new Trick;
        $user = $this->getUser();
        $trick->setUser($user);

        $form = $this->createForm(FormTrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['upload_file']->getData();
            if ($file) {
                $file_name = $file_uploader->upload($file);
                $original_file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                if (null !== $file_name) // for example
                {
                    $directory = $file_uploader->getTargetDirectory();
                    $full_path = $directory . '/' . $file_name;
                } else {
                    // Oups, an error occured !!!
                }
            }
            $media = new Media();
            $media->setName($original_file_name);
            $media->setUrl($file_name);
            $media->setTrick($trick);

            $video = new Video();
            $video_name = $form->get("video_name")->getData();
            $video_url = $form->get("video_url")->getData();
            $video->setName($video_name);
            $video->setUrl($video_url);
            $video->setTrick($trick);

            $em = $this->getDoctrine()->getManager();
            $em->persist($trick);
            $em->persist($media);
            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('/trick/new-trick.html.twig', [
            'newTrickForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/trick/{id}/{slug}", name="trick_show")
     */
    public function getSingleTrick($id, $slug, Request $request, PaginatorInterface $paginator): Response
    {
        $trick = $this->getDoctrine()->getRepository(Trick::class)->findOneBy(array('slug' => $slug));

        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy(array('trick' => $id), array('createdAt' => 'DESC'));

        $messages = $paginator->paginate(
            $trick->getMessages(),
            $request->query->getInt('page', 1),
            2
        );

        $message = new Message;

        $user = $this->getUser();
        $trick->setUser($user);

        $form = $this->createForm(FormMessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('home');
        }


        return $this->render(
            'trick/trick_show.html.twig',
            [
                'trick' => $trick,
                'messages' => $messages,
                'request' => $request,
                'newMessageForm' => $form->createView()
            ]
        );
    }
}
