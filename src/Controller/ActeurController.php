<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Acteur;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActeurController extends AbstractController
{
    /**
     * @Route("/acteur", name="acteur")
     */
    public function index(): Response
    {
        return $this->render('acteur/index.html.twig', [
            'controller_name' => 'ActeurController',
        ]);
    }

    public function accueil(Request $request) {
        

        /* Film form */
            $film_form = $this->createFormBuilder(null)
                ->add('film', TextType::class)
                ->add('submit', SubmitType::class, array('label' => 'Rechercher', 'attr' => ['class' => 'btn btn-primary']  ))
                ->getForm();
        /* Recherche par film */
        $film_form->handleRequest($request);
            if($film_form->isSubmitted() && $film_form->isValid()) { 
               
                $film = $film_form['film']->getData();
                
                $acteurs = $this->getDoctrine()->getRepository(Acteur::class)->findByFilm($film);
                //dd($acteurs);
                return $this->render('acteur/accueil.html.twig', array('acteurs' => $acteurs, 
                                    'myform' => $film_form->createView()
                                    /*'myform1' => $form1->createView(),
                                    'myform2' => $acteur_form->createView(),
                                    'myform3' => $titre_form->createView()*/ ));
            }
        /* Tous les acteurs */
        $acteurs = $this->getDoctrine()->getRepository(Acteur::class)->findAll();
            
            return $this->render('acteur/accueil.html.twig', array('acteurs' => $acteurs,
                                    'myform' => $film_form->createView()
                                    /*'myform1' => $form1->createView(),
                                    'myform2' => $acteur_form->createView(),
                                    'myform3' => $titre_form->createView())*/));
    }
}
