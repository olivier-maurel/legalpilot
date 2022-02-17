<?php

namespace App\Form;

use App\Entity\Character;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

class CharacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('strong', RangeType::class, [
                'label' => 'Force',
                'attr' => [
                    'min' => 1,
                    'max' => 7,
                    'class' => 'form-range col-lg-4'
                ]
            ])
            ->add('speed', RangeType::class, [
                'label' => 'Rapidité',
                'attr' => [
                    'min' => 1,
                    'max' => 7,
                    'class' => 'form-range col-lg-4'
                ]
            ])
            ->add('guard', RangeType::class, [
                'label' => 'Défense',
                'attr' => [
                    'min' => 1,
                    'max' => 7,
                    'class' => 'form-range col-lg-4'
                ]
            ])
            ->add('image', null, [
                'label' => 'Image (lien externe)',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Character::class,
        ]);
    }
}
