<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentRepository;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Pin;



/**
 * @method User getUser()
 */

class CommentController extends AbstractController
{
    /**
     * @Route("/ajax/comment", name="comment_add")
     */
    public function add(Request $request, PinRepository $pinRepo,UserRepository $userRepo,CommentRepository $commentRepo, EntityManagerInterface $em): Response
    {
        $commentData = $request->request->all('comment');

        if(!$this->isCsrfTokenValid('comment-add',$commentData['_token'])){
            return $this->json([
                'code' => 'INVALID_CSRF_TOKEN'
            ], Response::HTTP_BAD_REQUEST);
        }

        //Si un utilisateur rentre un id qui n'éxiste pas LOL
        $pin = $pinRepo->findOneBy(['id' => $commentData['pin']]);
        if(!$pin){
            return $this->json([
                'code' => 'PIN_NOT_FOUND',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'code' => 'USER_NOT_AUTHENTICATED_FULLY'
            ], Response::HTTP_BAD_REQUEST);
        }


        $comment = new Comment($pin);

        $comment->setContent($commentData['content']);
        $comment->setUser($user);
        //$comment->setCreatedAt(new \DateTime());

        $em->persist($comment);
        $em->flush();

        $html = $this->renderView('comment/index.html.twig', [
            'comment' => $comment
        ]);



        return $this->json([
            'code' => 'COMMENT_ADDED_SUCCESSFULLY',
            'message' => $html,
            'numberOfComments' => $commentRepo->count(['pin'=> $pin])

        ]);

        /*return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);*/
    }
}
