<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\ChoiceList;
use App\Entity\Field;
use App\Enum\DatumTypeEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $types = array_flip(DatumTypeEnum::getTypesLabels());

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => false,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => $types,
                'expanded' => false,
                'multiple' => false,
                'label' => false,
            ])
            ->add('position', HiddenType::class, [
                'required' => false,
            ])
            ->add('choiceList', EntityType::class, [
                'class' => ChoiceList::class,
                'required' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT,
            static function (FormEvent $event): void {
                $data = $event->getData();
                if (DatumTypeEnum::TYPE_LIST !== $data['type']) {
                    $data['choiceList'] = null;
                }

                $event->setData($data);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Field::class,
        ]);
    }
}
