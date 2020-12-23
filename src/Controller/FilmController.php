<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\Type\FilmType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    /**
     * @Route("/film", name="film")
     */

    public function index() {
        return $this->render('index.html.twig',array('test' => 'test'));
    }

    public function accueil(): Response {
        $films = $this->getDoctrine()
                ->getRepository(Film::class)->findAll();
        return $this->render('film/accueil.html.twig', array('films' => $films));
    }

    public function afficher($id) {
        $film = $this->getDoctrine()->getRepository(Film::class)->find($id);

        if(!$film) {
            throw $this->createNotFoundException('Film[id='.$id.'] inexistant');
        }
        
        return $this->render('film/afficher.html.twig',
                array('film' => $film));
    }
    /* Ajouter un nouveau film */
    public function ajouter(Request $request) {

        $film = new Film;
        //Create form
        $form = $this->createForm(FilmType::class, $film,
            ['action' => $this->generateUrl('film_ajouter')]);
        $form->add('submit', SubmitType::class, array('label' => 'Ajouter'));
        //Give request
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($film);
            $entityManager->flush();
            //Afficher le nouveau film ajouté
            return $this->redirectToRoute('film_accueil',
            array('id' => $film->getId()));
        }
            
        return $this->render('film/ajouter.html.twig',
            array('myform' => $form->createView()));
    }
    /* Modifier un film */
    public function modifier($id) {
        $film = $this->getDoctrine()->getRepository(Film::class)->find($id);
        if(!$film) {
            throw $this->createNotFoundException('Film[id='.$id.'] inexistante');
        }
        
        $form = $this->createForm(FilmType::class, $film,
            ['action' => $this->generateUrl('film_modifier_suite',
                array('id' => $film->getId()))]);

        $form->add('submit', SubmitType::class, array('label' => 'Modifier'));
        return $this->render('film/modifier.html.twig',
            array('myform' => $form->createView())); 
    }

    public function modifierSuite(Request $request,$id) {
        //Find film
        $film = $this->getDoctrine()->getRepository(Film::class)->find($id);
        if(!$film) {
            throw $this->createNotFoundException('Film[id='.$id.'] inexistante');
        }
        //Create form
        $form = $this->createForm(FilmType::class, $film,
            ['action' => $this->generateUrl('film_modifier_suite',
            array('id' => $film->getId()))]);

        $form->add('submit', SubmitType::class, array('label' => 'Modifier'));
        //Give request
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($film);
            $entityManager->flush();
            $url = $this->generateUrl('film_afficher',
                array('id' => $film->getId()));
            return $this->redirect($url);
        }
        return $this->render('film/modifier.html.twig',
            array('myform' => $form->createView()));    
    }

    public function supprimer($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Film::class);
        $film = new Film;
        //Find resto
        $film = $repo->find($id);
        //Persist film
        $em->persist($film);
        //Remove film
        $em->remove($film);
        //Flush 
        $em->flush();
        
        return $this->redirectToRoute('film_accueil',
            array('null' => 'null'));
    }
}
