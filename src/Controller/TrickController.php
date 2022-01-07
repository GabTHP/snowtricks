<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Media;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request; // Nous avons besoin d'accéder à la requête pour obtenir le numéro de page
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator

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
     * @Route("/trick/{id}/{slug}", name="trick_show")
     */
    public function getSingleTrick($id, $slug, Request $request, PaginatorInterface $paginator): Response
    {
        $trick = $this->getDoctrine()->getManager()->getRepository(Trick::class)->findOneBy(array('slug' => $slug));

        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy(array('trick' => $id), array('createdAt' => 'DESC'));

        $messages = $paginator->paginate(
            $messages,
            $request->query->getInt('page', 1),
            2
        );

        $medias = $this->getDoctrine()->getRepository(Media::class)->findBy(array('trick' => $id));

        $videos = $this->getDoctrine()->getRepository(Video::class)->findBy(array('trick' => $id));

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();


        return $this->render(
            'trick/trick_show.html.twig',
            [
                'controller_name' => 'TrickController',
                'trick' => $trick,
                'messages' => $messages,
                'users' => $users,
                'medias' => $medias,
                'videos' => $videos,
                'request' => $request
            ]
        );
    }
}
