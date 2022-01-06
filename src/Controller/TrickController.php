<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Media;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
                'controller_name' => 'TrickController',
                'tricks' => $tricks
            ]
        );
    }

    /**
     * @Route("/trick/{id}", name="trick_show")
     */
    public function getSingleTrick($id): Response
    {
        $trick = $this->getDoctrine()->getManager()->getRepository(Trick::class)->find($id);

        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy(array('trick' => $id), array('createdAt' => 'ASC'));

        $medias = $this->getDoctrine()->getRepository(Media::class)->findBy(array('trick' => $id));

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();





        return $this->render(
            'trick/trick_show.html.twig',
            [
                'controller_name' => 'TrickController',
                'trick' => $trick,
                'messages' => $messages,
                'users' => $users,
                'medias' => $medias
            ]
        );
    }
}
