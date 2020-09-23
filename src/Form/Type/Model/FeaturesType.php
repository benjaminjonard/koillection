<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeaturesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wishlistsFeatureActive', CheckboxType::class, [
                'required' => false
            ])
            ->add('tagsFeatureActive', CheckboxType::class, [
                'required' => false
            ])
            ->add('signsFeatureActive', CheckboxType::class, [
                'required' => false
            ])
            ->add('albumsFeatureActive', CheckboxType::class, [
                'required' => false
            ])
            ->add('loansFeatureActive', CheckboxType::class, [
                'required' => false
            ])
            ->add('templatesFeatureActive', CheckboxType::class, [
                'required' => false
            ])
            ->add('historyFeatureActive', CheckboxType::class, [
                'required' => false
            ])
            ->add('statisticsFeatureActive', CheckboxType::class, [
                'required' => false
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
