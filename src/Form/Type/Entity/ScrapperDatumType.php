<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Enum\DatumTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScrapperDatumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $types = [];
        foreach (DatumTypeEnum::AVAILABLE_FOR_SCRAPPING as $type) {
            $types[DatumTypeEnum::getTypeLabel($type)] = $type;
        }

        $builder
            ->add('name', TextType::class, [
                'attr' => ['length' => 255],
                'required' => true,
            ])
            ->add('path', TextType::class, [
                'required' => true,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => $types,
                'expanded' => false,
                'multiple' => false,
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
