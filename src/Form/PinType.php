<?php

namespace App\Form;

use App\Entity\Pin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $pin = $options['data'];
        $isEdit = $pin && $pin->getId();

        $builder
            ->add('imageFile', VichImageType::class,[
                'label' => 'Ajouter une Jaquette',
                'required' => false,
                'allow_delete' =>true,
                'download_label' => 'Télécharger la Jaquette',
                'download_uri' => true,
                'imagine_pattern' => 'squared_thumbnail_small',
                //'image_uri' => true,                
                //'asset_helper' =>true,
            ])
            ->add('title', TextType::class,[
                'label' => 'Titre du Jeu'
            ])
            ->add('description', TextareaType::class,[
                'label' => 'Contenu'
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pin::class,
        ]);
    }
}
