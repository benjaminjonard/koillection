<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Import;
use App\Form\DataTransformer\ImportMappingTransformer;
use App\Form\Type\Model\ImportMapperElementType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportMappingType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $import = $builder->getData();
        $choices = [];
        foreach ($import->getHeaders() as $index => $header) {
            $choices[$header] = $index;
        }

        $builder
            ->add('nameIndex', ChoiceType::class, [
                'required' => true,
                'choices' => $choices
            ])
            ->add('imageIndex', ChoiceType::class, [
                'required' => false,
                'choices' => $choices
            ])
            ->add('mapping', CollectionType::class, [
                'entry_type' => ImportMapperElementType::class,
                'entry_options' => [
                    'headers' => $choices,
                ],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
        ;

        $builder->get('mapping')->addModelTransformer(new ImportMappingTransformer());
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Import::class,
        ]);
    }
}
