<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Datum;
use App\Form\Type\MediumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DatumType
 *
 * @package App\Form\Type\Entity
 */
class DatumType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', TextType::class, [
                'required' => true,
            ])
            ->add('label', TextType::class, [
                'required' => false,
            ])
            ->add('value', TextType::class, [
                'required' => false,
            ])
            ->add('image', MediumType::class, [
                'required' => false,
            ])
            ->add('position', TextType::class, [
                'required' => false,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Datum::class
        ]);
    }
}
