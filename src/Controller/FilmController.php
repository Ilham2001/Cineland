<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Acteur;
use App\Form\Type\FilmType;
use App\Form\Type\FiltreFilmType;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

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

    public function accueil(Request $request) {

        /* Depart et arrivée date Form */
        $form = $this->createFormBuilder(null)
                ->add('dateDepart', BirthdayType::class)
                ->add('dateArrivee',BirthdayType::class)
                ->add('submit', SubmitType::class, array('label' => 'Rechercher', 'attr' => ['class' => 'btn btn-primary']))
                ->getForm();
        
        /* Titre film form */
        $titre_form = $this->createFormBuilder(null)
                ->add('titre', TextType::class)
                ->add('submit', SubmitType::class, array('label' => 'Rechercher', 'attr' => ['class' => 'btn btn-primary']  ))
                ->getForm();

        /* Recherche par titre de film */
            $titre_form->handleRequest($request);
            if($titre_form->isSubmitted() && $titre_form->isValid()) { 
               
                $titre = $titre_form['titre']->getData();

                $films = $this->getDoctrine()->getRepository(Film::class)->findByTitle($titre);
                return $this->render('film/accueil.html.twig', array('films' => $films, 
                    'myform' => $titre_form->createView()));
            }
        
        /* Tous les films */
            $films = $this->getDoctrine()->getRepository(Film::class)->findAll();
            
            return $this->render('film/accueil.html.twig', array('films' => $films,
                'myform' => $titre_form->createView()));
    }

    public function afficher($id) {
        $film = $this->getDoctrine()->getRepository(Film::class)->find($id);

        if(!$film) {
            throw $this->createNotFoundException('Film[id='.$id.'] inexistant');
        }
        
        return $this->render('film/accueil.html.twig',
                array('film' => $film));
    }
    /* Ajouter un nouveau film */
    public function ajouter(Request $request) {

        $film = new Film;
        //Create form
        $add_form = $this->createForm(FilmType::class, $film, ['action' => $this->generateUrl('film_ajouter')]);
        $add_form->add('submit', SubmitType::class, array('label' => 'Ajouter'));

        $add_form->handleRequest($request);
        if ($add_form->isSubmitted()) {
            dd($add_form);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($film);
            $entityManager->flush();
            //Afficher le nouveau film ajouté
            dd('done');
            return $this->redirectToRoute('film_afficher',
            array('id' => $film->getId()));
        }
            
        return $this->render('film/ajouter.html.twig',
            array('myform' => $add_form->createView()));
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

    public function filtreDates(Request $request) {
        
        $form = $this->createFormBuilder(null)
                ->add('dateDepart', BirthdayType::class)
                ->add('dateArrivee',BirthdayType::class)
                ->add('submit', SubmitType::class, array('label' => 'Rechercher', 'attr' => ['class' => 'btn btn-primary']))
                ->getForm();

        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $dateDepart = $form['dateDepart']->getData();
                $dateArrivee = $form['dateArrivee']->getData();
                $films = $this->getDoctrine()->getRepository(Film::class)->findByStartAndEndDate($dateDepart,$dateArrivee);
                return $this->render('film/filtreDates.html.twig', array('films' => $films, 
                    'myform' => $form->createView()));
            }

        $films = $this->getDoctrine()->getRepository(Film::class)->findAll();

        return $this->render('film/filtreDates.html.twig',array('films' => $films,
            'myform' => $form->createView()));
        
    }

    public function filtreDateAnterieure(Request $request) {
        
        $form = $this->createFormBuilder(null)
            ->add('dateAvant', BirthdayType::class)
            ->add('submit', SubmitType::class, array('label' => 'Rechercher', 'attr' => ['class' => 'btn btn-primary'] ))
            ->getForm();

        $form->handleRequest($request);          
        if ($form->isSubmitted() && $form->isValid()) {
            $dateAvant = $form['dateAvant']->getData();
            $films = $this->getDoctrine()->getRepository(Film::class)->findByBeforeDate($dateAvant);
            
            return $this->render('film/filtreDateAnterieure.html.twig', array('films' => $films, 
                'myform' => $form->createView()));
        }
        $films = $this->getDoctrine()->getRepository(Film::class)->findAll();

        return $this->render('film/filtreDateAnterieure.html.twig',array('films' => $films,
            'myform' => $form->createView()));
        
    }

    public function filtreActeur(Request $request) {
        
        $form = $this->createFormBuilder(null)
            ->add('acteur', TextType::class)
            ->add('submit', SubmitType::class, array('label' => 'Rechercher', 'attr' => ['class' => 'btn btn-primary']  ))
            ->getForm();
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) { 
               
            $acteur = $form['acteur']->getData();
                
            $films = $this->getDoctrine()->getRepository(Film::class)->findByActeur($acteur);

            return $this->render('film/filtreActeur.html.twig', array('films' => $films,                 
                'myform' => $form->createView()));
            }

        $films = $this->getDoctrine()->getRepository(Film::class)->findAll();

        return $this->render('film/filtreActeur.html.twig',array('films' => $films,
                'myform' => $form->createView()));
    }

    public function filtreGenre(Request $request) {
    
        $form = $this->createFormBuilder(null)
            ->add('genre', TextType::class)
            ->add('submit', SubmitType::class, array('label' => 'Rechercher', 'attr' => ['class' => 'btn btn-primary']  ))
            ->getForm();
        $moyenne = "";
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $genre = $form['genre']->getData();
                //Récupérer les films
                $films = $this->getDoctrine()->getRepository(Film::class)->findByGenre($genre);
                //Calculer moyenne durée et retourner
               $somme =1;
                foreach ($films as $film) {
                    //dd($film->getDuree());
                    $somme += $film->getDuree();
                }
                $moyenne = $somme/count($films);
                
                return $this->render('film/filtreGenre.html.twig', array('films' => $films,
                    'moyenne' => $moyenne,
                    'myform' => $form->createView()));
            }

        $films = $this->getDoctrine()->getRepository(Film::class)->findAll();
        return $this->render('film/filtreGenre.html.twig',array('films' => $films,
            'moyenne' => $moyenne,
            'myform' => $form->createView()));
    }

    public function filtreDeuxActeurs(Request $request) {
        $form = $this->createFormBuilder(null)
            ->add('acteur1', TextType::class)
            ->add('acteur2', TextType::class)
            ->add('submit', SubmitType::class, array('label' => 'Rechercher', 'attr' => ['class' => 'btn btn-primary']  ))
            ->getForm();
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $acteur1 = $form['acteur1']->getData();
            $films_acteur1 = $this->getDoctrine()->getRepository(Film::class)->findByActeur($acteur1);

            $acteur2 = $form['acteur2']->getData();
            $films_acteur2 = $this->getDoctrine()->getRepository(Film::class)->findByActeur($acteur2);
            
            $films = array_intersect($films_acteur1, $films_acteur2);

            return $this->render('film/filtreDeuxActeurs.html.twig',array('films' => $films,
                'myform' => $form->createView()));
        }
        
        $films = $this->getDoctrine()->getRepository(Film::class)->findAll();
        return $this->render('film/filtreDeuxActeurs.html.twig',array('films' => $films,
            'myform' => $form->createView()));
    }
}