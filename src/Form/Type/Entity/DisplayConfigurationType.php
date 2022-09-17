<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\DisplayConfiguration;
use App\Entity\Item;
use App\Entity\Wishlist;
use App\Enum\DatumTypeEnum;
use App\Enum\DisplayModeEnum;
use App\Enum\SortingDirectionEnum;
use App\Repository\DatumRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisplayConfigurationType extends AbstractType
{
    private array $preSubmitColumns = [];

    public function __construct(private readonly DatumRepository $datumRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entity = $options['parentEntity'];

        $builder
            ->add('displayMode', ChoiceType::class, [
                'choices' => array_flip(DisplayModeEnum::getDisplayModeLabels()),
                'required' => true,
            ])
        ;

        if (in_array($options['class'], [Collection::class, Item::class])) {
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
            ;
        }

        if ($options['class'] == Collection::class) {
            $builder
                ->add('showNumberOfChildren', CheckboxType::class, [
                    'required' => false,
                ])
                ->add('showNumberOfItems', CheckboxType::class, [
                    'required' => false,
                ])
            ;
        }

        if ($options['class'] === Item::class || $options['class'] == Collection::class) {
            if ($options['class'] === Item::class) {
                $sortingProperties = [
                    'form.item_sorting.default_value' => null,
                ];

                $displayConfiguration = $entity->getItemsDisplayConfiguration();
                $labelsAvailableForOrdering = $this->datumRepository->findAllItemsLabelsInCollection($entity, DatumTypeEnum::AVAILABLE_FOR_ORDERING);
                $labelsAvailableForColumns = $this->datumRepository->findAllItemsLabelsInCollection($entity, DatumTypeEnum::TEXT_TYPES);
            } else {
                $sortingProperties = [
                    'form.item_sorting.default_value' => null,
                ];

                $displayConfiguration = $entity->getChildrenDisplayConfiguration();
                $labelsAvailableForOrdering = $this->datumRepository->findAllChildrenLabelsInCollection($entity, DatumTypeEnum::AVAILABLE_FOR_ORDERING);
                $labelsAvailableForColumns = $this->datumRepository->findAllChildrenLabelsInCollection($entity, DatumTypeEnum::TEXT_TYPES);
            }

            foreach ($labelsAvailableForOrdering as $label) {
                $sortingProperties[$label['label']] = $label['label'];
            }

            // Extract possible columns for a collection based on items
            $columns = [];
            foreach ($labelsAvailableForColumns as $label) {
                $columns[$label['label']] = $label['label'];
            }

            // Move already selected columns to the top of the array
            $alreadySelectedColumns = [];
            if ($displayConfiguration->getColumns()) {
                $alreadySelectedColumns = array_reverse($displayConfiguration->getColumns());
            }

            foreach ($alreadySelectedColumns as $alreadySelectedColumn) {
                unset($columns[$alreadySelectedColumn]);
                array_unshift($columns, [$alreadySelectedColumn => $alreadySelectedColumn]);
            }

            $builder
                ->add('sortingProperty', ChoiceType::class, [
                    'choices' => $sortingProperties,
                    'required' => true,
                ])
                ->add('sortingDirection', ChoiceType::class, [
                    'choices' => array_flip(SortingDirectionEnum::getSortingDirectionLabels()),
                    'required' => true,
                ])
                ->add('columns', ChoiceType::class, [
                    'choices' => $columns,
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                ])
            ;

            $builder->addEventListener(FormEvents::POST_SUBMIT, static function (FormEvent $event) use ($labelsAvailableForColumns): void {
                $displayConfiguration = $event->getData();
                $found = false;
                foreach ($labelsAvailableForColumns as $label) {
                    if ($label['label'] === $displayConfiguration->getSortingProperty()) {
                        $displayConfiguration->setSortingType($label['type']);
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $displayConfiguration->setSortingType(null);
                }
            });

            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
                if (isset($event->getData()['columns'])) {
                    $this->preSubmitColumns = $event->getData()['columns'];
                }
            });

            $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
                $data = $event->getData();
                $data->setColumns($this->preSubmitColumns);

                $event->setData($data);
            });
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DisplayConfiguration::class,
        ]);

        $resolver->setRequired([
            'class',
            'parentEntity'
        ]);
    }
}
