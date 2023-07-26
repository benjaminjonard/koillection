<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Scrapper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScrapperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['length' => 255],
                'required' => true
            ])
            ->add('namePath', TextType::class, [
                'required' => false
            ])
            ->add('imagePath', TextType::class, [
                'required' => false
            ])
            ->add('dataPaths', SymfonyCollectionType::class, [
                'entry_type' => ScrapperDatumType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Scrapper::class,
        ]);
    }
}
