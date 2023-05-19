<?php

declare(strict_types=1);

namespace App\Form\Type\Entity\Admin;

use App\Enum\ConfigurationEnum;
use App\Repository\ConfigurationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('thumbnailsFormat', ChoiceType::class, [
                'choices' => array_flip(ConfigurationEnum::geThumbnailFormatsLabels()),
                'placeholder' => ConfigurationEnum::geThumbnailFormatsDefaultLabel(),
                'data' => $options['thumbnailsFormat'],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);

        $resolver->setRequired([
            'thumbnailsFormat'
        ]);
    }
}
