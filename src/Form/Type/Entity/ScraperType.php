<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Scraper;
use App\Enum\ScraperContentTypeEnum;
use App\Enum\ScraperTypeEnum;
use App\Form\Type\Model\HeaderType;
use App\Model\Scraper\Header;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScraperType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $scraper = $builder->getData();

        $builder
            ->add('name', TextType::class, [
                'attr' => ['length' => 255],
                'required' => true
            ])
            ->add('contentType', ChoiceType::class, [
                'required' => true,
                'choices' => array_flip(ScraperContentTypeEnum::getContentTypeLabels())
            ])
            ->add('urlPattern', TextType::class, [
                'required' => false
            ])
            ->add('namePath', TextType::class, [
                'required' => false
            ])
            ->add('imagePath', TextType::class, [
                'required' => false
            ])
            ->add('headers', SymfonyCollectionType::class, [
                'entry_type' => HeaderType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;

        // Convert headers from array (database) to objects (used by the form)
        $builder->get('headers')->addModelTransformer(
            new CallbackTransformer(
                function (?array $array): array {
                    if (empty($array)) {
                        return [];
                    }

                    return array_map(function (array $header) {
                        return new Header()
                            ->setHeader($header['header'])
                            ->setValue($header['value'])
                        ;
                    }, $array);
                },

                function (?array $headers): array {
                    if (empty($headers)) {
                        return [];
                    }

                    return array_map(function (Header $header) {
                        return [
                            'header' => $header->getHeader(),
                            'value' => $header->getValue(),
                        ];
                    }, $headers);
                }
            )
        );

        $formModifier = static function (FormInterface $form) use ($scraper): void {
            match ($scraper->getType()) {
                ScraperTypeEnum::TYPE_COLLECTION => $form
                    ->add('dataPaths', SymfonyCollectionType::class, [
                        'entry_type' => PathType::class,
                        'entry_options' => ['for_class' => Collection::class],
                        'label' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                    ]),
                ScraperTypeEnum::TYPE_ITEM => $form
                    ->add('dataPaths', SymfonyCollectionType::class, [
                        'entry_type' => PathType::class,
                        'entry_options' => ['for_class' => Item::class],
                        'label' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                    ]),
                ScraperTypeEnum::TYPE_WISH => $form
                    ->add('pricePath', TextType::class, [
                        'required' => false,
                    ])
            };
        };

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            static function (FormEvent $event) use ($formModifier): void {
                $formModifier($event->getForm());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            static function (FormEvent $event) use ($formModifier): void {
                $formModifier($event->getForm());
            }
        );
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Scraper::class,
        ]);
    }
}
