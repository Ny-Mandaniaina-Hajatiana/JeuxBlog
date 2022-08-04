<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserFormType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ChangePasswordFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="app_account")
     */
    public function show(): Response
    {
        if (! $this->getUser()) {
            $this->addFlash('error', "Connecté-vous d'abord");
            return $this->redirectToRoute('app_login');
        }

        return $this->render('account/show.html.twig');
    }


    /**
     * @Route("/account/edit", name="app_account_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        if (! $this->getUser()) {
            $this->addFlash('error', "Connecté-vous d'abord");
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Compte mise à jours avec succès');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/change-password", name="app_account_change_password", methods={"GET","POST"})
     */
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if (! $this->getUser()) {
            $this->addFlash('error', "Connecté-vous d'abord");
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class,null,[
            'current_password_is_required' => true
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword($user,$form['plainPassword']->getData()));
        

            $em->flush();

            $this->addFlash('success', 'Mot de passe mise à jour');

            return $this->redirectToRoute('app_account');           
        }

        return $this->render('account/change_password.html.twig',[
            'form' => $form->createView()
        ]);
    }

}
