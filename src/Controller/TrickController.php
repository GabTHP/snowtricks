<?php

namespace App\Controller;

use App\Entity\Trick;
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
        var_dump($tricks);
        exit;

        return $this->render(
            'trick/index.html.twig',
            [
                'controller_name' => 'TrickController',
                'tricks' => $tricks
            ]
        );
    }
}
