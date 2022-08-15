<?php

namespace App\Form;

use App\Entity\Pin;
use App\Entity\Comment;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;



class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('content', TextareaType::class,[
            'label' => 'Votre commentaire'
        ])
        ->add('pin', HiddenType::class)
        
        ->add('send', SubmitType::class,[
            'label' => 'Commenter',
            'attr' => [
                'class' => 'btn', 
                'style' => 'color:#fff; background-color:#dc3545;margin-top:15px'
            ]
        ]);
            $builder->get('pin')
            ->addModelTransformer(new CallbackTransformer(
                fn (Pin $pins) => $pins->getId(),
                fn (Pin $pins) => $pins->getTitle()
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'csrf_token_id' => 'comment-add'
        ]);
    }
}