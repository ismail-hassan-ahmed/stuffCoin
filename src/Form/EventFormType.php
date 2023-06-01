<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Event;

use Doctrine\DBAL\Types\DateTimeImmutableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class, [

                'attr' => [

                    'rows' => "8",
                    'placeholder' => "Veuillez décrire l'événement en quelques lignes..."
                ]
            ])
            ->add('location', TextType::class)
            ->add('price', MoneyType::class, [

                'attr' => [
                    'placeholder' => "Le prix de l'événement"
                ]
            ])
            ->add('startsAt')
            ->add('imageFile', VichImageType::class)
            ->add('category', EntityType::class, [
                'label' => "Catégories",
                'placeholder' => "--Choisir une catégorie--",
                'class'       => Category::class,
                'choice_label' => function (Category $category) {
                   return strtoupper($category->getName());
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
