<?php

namespace App\Controller;
use App\Entity\Genre;
use App\Form\Type\GenreType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    /**
     * @Route("/genre", name="genre")
     */
    public function accueil(): Response
    {
        $genres = $this->getDoctrine()->getRepository(Genre::class)->findAll();
        return $this->render('genre/accueil.html.twig', array('genres' => $genres));
    }

    public function ajouter(Request $request) {

        $genre = new genre;
        //Create form
        $form = $this->createForm(GenreType::class, $genre,
            ['action' => $this->generateUrl('genre_ajouter')]);
        $form->add('submit', SubmitType::class, array('label' => 'Ajouter'));
        //Give request
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($genre);
            $entityManager->flush();
            
            $genres = $this->getDoctrine()->getRepository(Genre::class)->findAll();
            return $this->render('genre/accueil.html.twig', array('genres' => $genres));
        }
            
        return $this->render('genre/ajouter.html.twig',
            array('myform' => $form->createView()));
    }

    public function supprimer($id) {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Genre::class);
        $genre = new Genre;
        //Find genre
        $genre = $repo->find($id);
        //$f = $genre->getFilms();
        //Persist genre
        $em->persist($genre);
        try {
            if(!empty($genre->getFilms)) {
                return $this->redirectToRoute('genre_accueil',
                array('null' => 'null'));
            }
            else {
                //Remove genre
                $em->remove($genre);
                $em->flush();
                return $this->redirectToRoute('genre_accueil',
                array('null' => 'null'));
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $response = New Response();
            $response -> setContent($errorMessage);
            $response -> setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
        }
        
    }
}
