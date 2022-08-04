<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use  App\Repository\PinRepository;
use App\Entity\Pin;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="app_search")
     */
    public function index(): Response
    {
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }

    public function searchBar()
    {
        $form = $this->createFormBuilder()
        ->setAction($this->generateUrl('handleSearch'))
        ->add('query',
        TextType::class,[
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Tapez votre recherche'
            ]
        ])
        ->add('recherche',
        SubmitType::class,[
            'label' => 'Rechercher',
            'attr' => [
                'class' => 'btn btn-secondary',               
            ]
        ])
        ->getForm();
        return $this->render('search/searchBar.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/handleSearch", name="handleSearch")
     * @param Request $request
     */

     public function handleSearch(Request $request, PinRepository $repo)
     {
        $query = $request->request->all('form')['query'];
         if ($query) {
             $pins = $repo->findpinsByName($query);
         }

         return $this->render('search/index.html.twig',[
             'pins' => $pins
         ]);
     }
}
