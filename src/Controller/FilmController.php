<?php

namespace App\Controller;

use App\Entity\Film;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    /**
     * @Route("/film", name="film")
     */
    public function accueil(): Response
    {
        $films = $this->getDoctrine()
                ->getRepository(Film::class)->findAll();
        return $this->render('film/index.html.twig', array('films' => $films));
    }

    public function afficher($id) {
        $film = $this->getDoctrine()->getRepository(Film::class)->find($id);

        if(!$film) {
            throw $this->createNotFoundException('Film[id='.$id.'] inexistant');
        }
        
        return $this->render('film/afficher.html.twig',
                array('film' => $film));
    }
}
