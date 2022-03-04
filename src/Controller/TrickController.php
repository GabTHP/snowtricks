<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Media;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Video;
use App\Form\FormTrickType;
use App\Form\FormMessageType;
use App\Form\FormMediaType;
use App\Form\FormVideoType;
use App\Repository\MediaRepository;
use App\Repository\MessageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request; // Nous avons besoin d'accéder à la requête pour obtenir le numéro de page
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;

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
    public function newTrick(Request $request,  FileUploader $file_uploader, EntityManagerInterface $em)
    {
        $trick = new Trick;
        $user = $this->getUser();
        $trick->setUser($user);

        $form_new = $this->createForm(FormTrickType::class, $trick);

        $form_new->handleRequest($request);

        if ($form_new->isSubmitted() && $form_new->isValid()) {

            $file = $form_new['upload_file']->getData();
            if ($file) {
                $file_name = $file_uploader->upload($file);
                $original_file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $media = new Media();
                $media->setName($original_file_name);
                $media->setUrl($file_name);
                $media->setTrick($trick);
                $em->persist($media);

                if (null !== $file_name) // for example
                {
                    $directory = $file_uploader->getTargetDirectory();
                    $full_path = $directory . '/' . $file_name;
                } else {
                    // Oups, an error occured !!!
                }
            }

            $file2 = $form_new['mainMedia']->getData();
            if ($file2) {
                $file_name = $file_uploader->upload($file2);
                $original_file_name = pathinfo($file2->getClientOriginalName(), PATHINFO_FILENAME);
                $trick->setMainMedia($original_file_name);
                $trick->setMainMediaUrl($file_name);
            }
            $video_name = $form_new->get("video_name")->getData();
            $video_url = $form_new->get("video_url")->getData();
            if ($video_name !== null) {
                $video = new Video();
                $video->setName($video_name);
                $video->setUrl($video_url);
                $video->setTrick($trick);
                $em->persist($video);
            }
            $em->persist($trick);
            $em->flush();
            $this->addFlash("notice", "Le trick a été ajouté avec succés !");

            return $this->redirectToRoute('home');
        }

        return $this->render('/trick/new-trick.html.twig', [
            'newTrickForm' => $form_new->createView()
        ]);
    }

    /**
     * @Route("/edit-trick/{id}", name="edit_trick")
     */
    public function editTrick($id, TrickRepository $repo, Request $request,  FileUploader $file_uploader, EntityManagerInterface $em)
    {

        $trick = $repo->findOneBy(['id' => $id]);
        $form_edit = $this->createForm(FormTrickType::class, $trick);

        $form_edit->handleRequest($request);

        if ($form_edit->isSubmitted() && $form_edit->isValid()) {

            $file = $form_edit['upload_file']->getData();
            if ($file) {
                $file_name = $file_uploader->upload($file);
                $original_file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $media = new Media();
                $media->setName($original_file_name);
                $media->setUrl($file_name);
                $media->setTrick($trick);
                $em->persist($media);

                if (null !== $file_name) {
                    $directory = $file_uploader->getTargetDirectory();
                    $full_path = $directory . '/' . $file_name;
                }
            }
            $file2 = $form_edit['mainMedia']->getData();
            if ($file2) {
                $file_name = $file_uploader->upload($file2);
                $original_file_name = pathinfo($file2->getClientOriginalName(), PATHINFO_FILENAME);
                $trick->setMainMedia($original_file_name);
                $trick->setMainMediaUrl($file_name);
            }

            $video_name = $form_edit->get("video_name")->getData();
            $video_url = $form_edit->get("video_url")->getData();
            if ($video_name !== null) {
                $video = new Video();
                $video->setName($video_name);
                $video->setUrl($video_url);
                $video->setTrick($trick);
                $em->persist($video);
            }
            $em->persist($trick);

            $em->flush();
            $this->addFlash("notice", "Le trick a été mis à jour avec succés !");
            return $this->redirectToRoute('edit_trick', array('id' => $id));
        }

        return $this->render('/trick/edit-trick.html.twig', [
            'editTrickForm' => $form_edit->createView(),
            'trick' => $trick,
        ]);
    }

    /**
     * @Route("/delete-trick/{id}", name="delete_trick")
     */
    public function removeTrick($id, TrickRepository $repo, EntityManagerInterface $em)
    {
        $trick = $repo->find($id);
        $em->remove($trick);
        $em->flush();
        $this->addFlash("notice", "Le trick a été supprimé avec succés !");



        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/edit-trick/{trick_id}/delete-media/{id}", name="delete_media")
     */
    public function removeMedia($trick_id, $id, MediaRepository $repo, EntityManagerInterface $em)
    {
        $media = $repo->findOneBy(['id' => $id]);
        $em->remove($media);
        $em->flush();

        $this->addFlash("notice", "L'image a été supprimée avec succés !");

        return $this->redirectToRoute('edit_trick', array('id' => $trick_id));
    }

    /**
     * @Route("/edit-trick/{trick_id}/delete-video/{id}", name="delete_video")
     */
    public function removeVideo($trick_id, $id, VideoRepository $repo, EntityManagerInterface $em)
    {
        $video = $repo->findOneBy(['id' => $id]);
        $em->remove($video);
        $em->flush();

        $this->addFlash("notice", "La vidéo a été supprimée avec succés !");

        return $this->redirectToRoute('edit_trick', array('id' => $trick_id));
    }

    /**
     * @Route("/edit-trick/delete-main-media/{id}", name="delete_main_media")
     */
    public function removeMainMedia($id, TrickRepository $repo, EntityManagerInterface $em)
    {
        $trick = $repo->findOneBy(['id' => $id]);
        $trick->setMainMedia(null);
        $trick->setMainMediaUrl(null);
        $em->persist($trick);
        $em->flush();

        $this->addFlash("notice", "L'image principale du trick a été supprimée avec succés !");

        return $this->redirectToRoute('edit_trick', array('id' => $id));
    }



    /**
     * @Route("/trick/{id}/{slug}", name="trick_show")
     */
    public function getSingleTrick($id, $slug, TrickRepository $repo, MessageRepository $repo2, Request $request, EntityManagerInterface $em): Response
    {
        $trick = $repo->findOneBy(array('slug' => $slug));

        $message = new Message;


        $form = $this->createForm(FormMessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $message->setUser($user);
            $message->SetTrick($trick);
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('trick_show', array('id' => $id, 'slug' => $slug));
        }
        $trick = $repo->findOneBy(array('slug' => $slug));
        $messages = $repo2->findBy(['trick' => $id], ['createdAt' => 'DESC'], 5, 0);

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
     * @Route("/messages/{id}/{start}", name="loadMoreMessages", requirements={"start": "\d+"})
     */
    public function loadMoreMessages($id, MessageRepository $repo2, Request $request, $start = 5)
    {

        $messages = $repo2->findBy(['trick' => $id], ['createdAt' => 'DESC'], 5, $start);

        return $this->render(
            'trick/load-more-messages.html.twig',
            [
                'request' => $request,
                'messages' =>  $messages,
            ]
        );
    }

    /**
     * @Route("/edit-trick/{trick_id}/edit-media/{id}", name="edit_media")
     */
    public function editMedia($trick_id, $id, MediaRepository $repo, Request $request,  FileUploader $file_uploader, EntityManagerInterface $em)
    {
        $media = $repo->findOneBy(['id' => $id]);

        $form_edit = $this->createForm(FormMediaType::class);

        $form_edit->handleRequest($request);

        if ($form_edit->isSubmitted() && $form_edit->isValid()) {

            $file = $form_edit['media']->getData();
            if ($file) {
                $file_name = $file_uploader->upload($file);
                $original_file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $media->setName($original_file_name);
                $media->setUrl($file_name);
                $em->persist($media);
            }

            $em->persist($media);
            $em->flush();

            $this->addFlash("notice", "L'image a été mise à jour avec succés !");

            return $this->redirectToRoute('edit_trick', array('id' => $trick_id));
        }

        return $this->render('/trick/edit-media.html.twig', [
            'editMediaForm' => $form_edit->createView(),
            'media' => $media,
        ]);
    }

    /**
     * @Route("/edit-trick/{trick_id}/edit-video/{id}", name="edit_video")
     */
    public function editVideo($trick_id, $id, VideoRepository $repo, Request $request, EntityManagerInterface $em)
    {
        $video = $repo->findOneBy(['id' => $id]);
        $form_edit = $this->createForm(FormVideoType::class);

        $form_edit->handleRequest($request);

        if ($form_edit->isSubmitted() && $form_edit->isValid()) {

            $video_url = $form_edit['video_url']->getData();
            $video_name = $form_edit['video_name']->getData();
            $video->setName($video_name);
            $video->setUrl($video_url);

            $em->persist($video);
            $em->flush();

            $this->addFlash("notice", "La vidéo a été mise à jour avec succés !");

            return $this->redirectToRoute('edit_trick', array('id' => $trick_id));
        }

        return $this->render('/trick/edit-video.html.twig', [
            'editVideoForm' => $form_edit->createView(),
            'video' => $video,
        ]);
    }
}
