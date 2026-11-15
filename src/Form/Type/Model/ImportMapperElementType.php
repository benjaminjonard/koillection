<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use App\Model\ImportMapperElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportMapperElementType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $types = [];
        foreach (DatumTypeEnum::AVAILABLE_FOR_IMPORT as $type) {
            $types[$this->translator->trans(DatumTypeEnum::getTypeLabel($type))] = $type;
        }

        $builder
            ->add('columnIndex', ChoiceType::class, [
                'label' => false,
                'required' => true,
                'choices' => $options['headers'],
            ])
            ->add('datumType', ChoiceType::class, [
                'label' => false,
                'required' => true,
                'choices' => $types,
            ])
            ->add('datumLabel', TextType::class, [
                'label' => false,
                'required' => true,
            ])
            ->add('datumVisibility', ChoiceType::class, [
                'label' => false,
                'choices' => array_flip(VisibilityEnum::getVisibilityLabels()),
                'required' => true,
            ])
            ->add('datumPosition', HiddenType::class, [
                'required' => false,
            ])
            ->add('createCorrespondingTags', CheckboxType::class, [
                'label' => false,
                'required' => false
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImportMapperElement::class,
            'headers' => [],
        ]);
    }
}
