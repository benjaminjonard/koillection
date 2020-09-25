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
            ->add('wishlistsFeatureEnabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('tagsFeatureEnabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('signsFeatureEnabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('albumsFeatureEnabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('loansFeatureEnabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('templatesFeatureEnabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('historyFeatureEnabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('statisticsFeatureEnabled', CheckboxType::class, [
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
