<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Collection;
use App\Entity\DisplayConfiguration;
use App\Enum\DisplayModeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisplayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('displayMode', ChoiceType::class, [
                'choices' => array_flip(DisplayModeEnum::getDisplayModeLabels()),
                'required' => true,
            ])
        ;

        if ($options['class'] === Collection::class) {
            $builder
                ->add('label', TextType::class, [
                    'attr' => ['length' => 255],
                    'required' => false,
                ])
                ->add('showVisibility', CheckboxType::class, [
                    'required' => false,
                ])
                ->add('showActions', CheckboxType::class, [
                    'required' => false,
                ])
                ->add('showNumberOfChildren', CheckboxType::class, [
                    'required' => false,
                ])
                ->add('showNumberOfItems', CheckboxType::class, [
                    'required' => false,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DisplayConfiguration::class,
        ]);

        $resolver->setRequired([
            'class',
        ]);
    }
}
