<?php

namespace App\Form\Type;

use App\Entity\Sections;
use App\Entity\Informations;
use Symfony\Component\Form\FormEvent;
use App\Repository\SectionsRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// ...

class DynamicType extends AbstractType
{
    // ...

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = $options['categories'];

        $builder
            ->add('category', ChoiceType::class, [
                'choices' => $categories,
                'choice_label' => function ($category) {
                    return $category->getName();
                },
                'choice_value' => function ($category) {
                    return $category ? $category->getId() : '';
                },
                'label' => 'Category',
                'required' => false,
                'mapped' => false,
            ])
            ->add('section', EntityType::class, [
                // looks for choices from this entity
                'class' => Sections::class,
                'query_builder' => function (SectionsRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->where('s.Category = :category')
                        ->setParameter('category', 2);
                },
                'choice_label' => 'name',

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('name', TextType::class)
            ->add('save', SubmitType::class)
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();

                    $data = $event->getData();
                    $section = $data['section'];

                    $form->remove('section');
                    $form->add('section', EntityType::class, [
                        'class' => Sections::class,
                        'query_builder' => function (SectionsRepository $er) use ($section) {
                            return $er->createQueryBuilder('s')
                                ->where('s.id = :id')
                                ->setParameter('id', $section);
                        },
                    ]);
                }
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Informations::class,
            'categories' => [],
        ]);
    }
}
