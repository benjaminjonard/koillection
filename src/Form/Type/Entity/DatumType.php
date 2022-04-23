<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Datum;
use App\Enum\DatumTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class DatumType extends AbstractType
{
    public function __construct(
        private Security $security
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'required' => true,
            ])
            ->add('label', TextType::class, [
                'required' => false,
            ])
            ->add('fileImage', FileType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('fileFile', FileType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('position', TextType::class, [
                'required' => false,
            ])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                switch ($data['type']) {
                    case DatumTypeEnum::TYPE_RATING:
                        $form
                            ->add('value', ChoiceType::class, [
                                'choices' => array_combine(range(1, 10), range(1, 10)),
                                'expanded' => true,
                                'multiple' => false,
                                'required' => false,
                            ])
                        ;
                        break;
                    case DatumTypeEnum::TYPE_DATE:
                        $form
                            ->add(
                                'value',
                                DateType::class,
                                [
                                'required' => false,
                                'html5' => false,
                                'widget' => 'single_text',
                                'format' => $this->security->getUser()->getDateFormatForForm(),
                                'model_transformer' => new CallbackTransformer(
                                    function ($string) {
                                        return null !== $string ? new \DateTime($string) : null;
                                    },
                                    function ($date) {
                                        return $date instanceof \DateTime ? $date->format('Y-m-d') : null;
                                    }
                                ), ]
                            )
                        ;
                        break;
                    case DatumTypeEnum::TYPE_LINK:
                        $form
                            ->add('value', UrlType::class, [
                                'required' => false,
                            ])
                        ;
                        break;
                    default:
                        $form
                            ->add('value', TextType::class, [
                                'required' => false,
                            ])
                        ;
                        break;
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Datum::class,
        ]);
    }
}
