<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Media;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Video;
use App\Form\FormTrickType;
use App\Form\FormMessageType;
use App\Repository\MediaRepository;
use App\Repository\MessageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request; // Nous avons besoin d'accéder à la requête pour obtenir le numéro de page
use App\Service\FileUploader;

class TrickController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TrickRepository $repo): Response
    {
        $tricks = $repo->findBy([], ['createdAt' => 'DESC'], 5, 0);

        return $this->render(
            'trick/index.html.twig',
            [
                'tricks' => $tricks
            ]
        );
    }

    /**
     * 
     * @Route("/tricks/{start}", name="loadMoreTricks", requirements={"start": "\d+"})
     */
    public function loadMoreTricks(TrickRepository $repo, $start = 5)
    {
        $tricks = $repo->findBy([], ['createdAt' => 'DESC'], 5, $start);

        return $this->render('trick/load-more-tricks.html.twig', [
            'tricks' => $tricks
        ]);
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
     * @Route("/edit-trick/{id}", name="edit_trick")
     */
    public function editTrick($id, Trick $trick, Request $request,  FileUploader $file_uploader)
    {
        $trick = $this->getDoctrine()->getRepository(Trick::class)->findOneBy(array('id' => $id));

        $form = $this->createForm(FormTrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['upload_file']->getData();
            if ($file) {
                $file_name = $file_uploader->upload($file);
                $original_file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $media = new Media();
                $media->setName($original_file_name);
                $media->setUrl($file_name);
                $media->setTrick($trick);
                $em = $this->getDoctrine()->getManager();
                $em->persist($media);

                if (null !== $file_name) // for example
                {
                    $directory = $file_uploader->getTargetDirectory();
                    $full_path = $directory . '/' . $file_name;
                } else {
                    // Oups, an error occured !!!
                }
            }


            $video = new Video();
            $video_name = $form->get("video_name")->getData();
            $video_url = $form->get("video_url")->getData();
            $video->setName($video_name);
            $video->setUrl($video_url);
            $video->setTrick($trick);

            $em = $this->getDoctrine()->getManager();
            $em->persist($trick);
            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('/trick/edit-trick.html.twig', [
            'newTrickForm' => $form->createView(),
            'trick' => $trick,
        ]);
    }

    /**
     * @Route("/delete-trick/{id}", name="delete_trick")
     */
    public function removeTrick($id, TrickRepository $repo)
    {
        $trick = $repo->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($trick);
        $em->flush();



        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/edit-trick/delete-media/{id}", name="delete_media")
     */
    public function removeMedia($id, Media $media)
    {
        $media = $this->getDoctrine()->getRepository(Media::class)->findOneBy(array('id' => $id));
        $em = $this->getDoctrine()->getManager();
        $em->remove($media);
        $em->flush();



        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/edit-trick/delete-video/{id}", name="delete_video")
     */
    public function removeVideo($id, Video $video)
    {
        $video = $this->getDoctrine()->getRepository(Video::class)->findOneBy(array('id' => $id));
        $em = $this->getDoctrine()->getManager();
        $em->remove($video);
        $em->flush();



        return $this->redirectToRoute('home');
    }



    /**
     * @Route("/trick/{id}/{slug}", name="trick_show")
     */
    public function getSingleTrick($id, $slug, TrickRepository $repo, MessageRepository $repo2, Request $request): Response
    {
        $trick = $repo->findOneBy(array('slug' => $slug));

        $message = new Message;


        $form = $this->createForm(FormMessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $message->setUser($user);
            $message->SetTrick($trick);
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        $messages = $repo2->findBy([], ['createdAt' => 'DESC'], 5, 0);

        return $this->render(
            'trick/trick_show.html.twig',
            [
                'trick' => $trick,
                'request' => $request,
                'messages' =>  $messages,
                'newMessageForm' => $form->createView()
            ]
        );
    }

    /**
     * 
     * @Route("/messages/{start}", name="loadMoreMessages", requirements={"start": "\d+"})
     */
    public function loadMoreMessages(MessageRepository $repo2, Request $request, $start = 5)
    {


        $messages = $repo2->findBy([], ['createdAt' => 'DESC'], 5, $start);

        return $this->render(
            'trick/load-more-messages.html.twig',
            [
                'request' => $request,
                'messages' =>  $messages,
            ]
        );
    }
}
