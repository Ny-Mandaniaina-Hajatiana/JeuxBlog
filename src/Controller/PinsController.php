<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PinRepository;
use App\Entity\Pin;
use SebastianBergmann\CodeCoverage\Node\Builder;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PinType;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\CommentType;
use App\Entity\Comment;
use App\Entity\User;


class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods="GET")     //Méthode GET
     */
    public function index(PinRepository $pinRepository, PaginatorInterface $paginator, Request $request): Response
    {

        
        /*return $this->render('pins/index.html.twig', compact('pins'));*/

        //Reordonner par ordre de création(du dernier au premier(#ASC))
        $pins = $pinRepository->findBy([],['createdAt'=>'DESC']);
        
        $pins = $paginator->paginate(
            $pins,
            $request->query->getInt('page', 1),
            $limit= 6
        );

        return $this->render('pins/index.html.twig',[
            'pins' => $pins,
        ]);
    }
    

    //Afficher Pin

    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show" , methods={"GET","POST"})
     */
    public function show(Pin $pin): Response
    {
            $comment = new Comment($pin);
            $commentForm = $this->createForm(CommentType::class, $comment);

            //return $this->render('pins/show.html.twig', compact('pin'));

            return $this->renderForm('pins/show.html.twig', [
                'pin' => $pin,
                'commentForm' => $commentForm
            ]);
    }

    //Modifier Pin

    /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="app_pins_edit" , methods={"GET" , "POST"})
     */

    public function edit(Pin $pin, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if (! $this->getUser()) {
            $this->addFlash('error', "Connecté-vous d'abord");
            return $this->redirectToRoute('app_login');
        }

        if ($pin->getUser() !== $this->getUser()) {
            $this->addFlash('error', "accès réfusé");

            return $this->redirectToRoute('app_home');
        }


        $form = $this->createForm(PinType::class, $pin);
       

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid())
        {
//$em->persist($pin); le Pin éxiste déja
            $em->flush();

            $this->addFlash('info','Modification Reussie');

            return $this->redirectToRoute('app_home');
        }

            return $this->render('pins/edit.html.twig', ['pin' => $pin, 
                                                        'form' => $form->createView()]);
    }

    //Supprimer Pin

     /**
     * @Route("/pins/{id<[0-9]+>}/delete", name="app_pins_delete", methods={"GET"})
     */
    public function delete(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (! $this->getUser()) {
            $this->addFlash('error', "Connecté-vous d'abord");
            return $this->redirectToRoute('app_login');
        }

        if ($pin->getUser() !== $this->getUser()) {
            $this->addFlash('error', "Accès réfusé");

            return $this->redirectToRoute('app_home');
        }
            //if ($this->isCsrfTokenValid('pin_delete'. $pin->getId(), $request->request->get('csrf_token'))) {
               
            //}
            $em->remove($pin);//pour supprimmer
            $em->flush();

            $this->addFlash('error', 'Suppression reussie');
            
            return $this->redirectToRoute('app_home');
    }

    //Cree Pin

     /**
     * @Route("/pins/create", name="app_pins_create", methods={"GET" , "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (! $this->getUser()) {
            $this->addFlash('error', "Connecté-vous d'abord");
            return $this->redirectToRoute('app_login');
        }

        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);//Pour recuperer les données du formulaire

        if($form->isSubmitted() && $form->isValid())
        {
            
            $pin->setUser($this->getUser());
            $em->persist($pin); //a besoin de l'enityManager(EntityManagerInterface)
            $em->flush();

            $this->addFlash('success', 'Ajout avec succès');

            return $this->redirectToRoute('app_home');
        }

            return $this->render('pins/create.html.twig', ['form'=>$form->createView()]);
    }
}