<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Collection;
use App\Entity\Template;
use App\Enum\DatumTypeEnum;
use App\Enum\DisplayModeEnum;
use App\Enum\SortingDirectionEnum;
use App\Enum\VisibilityEnum;
use App\Form\DataTransformer\Base64ToImageTransformer;
use App\Repository\CollectionRepository;
use App\Repository\DatumRepository;
use App\Repository\TemplateRepository;
use App\Service\FeatureChecker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
    private array $preSubmitItemsListColumns = [];

    public function __construct(
        private readonly Base64ToImageTransformer $base64ToImageTransformer,
        private readonly FeatureChecker $featureChecker,
        private readonly CollectionRepository $collectionRepository,
        private readonly TemplateRepository $templateRepository,
        private readonly DatumRepository $datumRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entity = $builder->getData();

        $itemsSortingChoices = [
            'form.item_sorting.default_value' => null,
        ];
        $labels = $this->datumRepository->findAllLabelsInCollection($entity, DatumTypeEnum::AVAILABLE_FOR_ORDERING);
        foreach ($labels as $label) {
            $itemsSortingChoices[$label['label']] = $label['label'];
        }

        // Extract possible columns for a collection based on items
        $itemsListColumnsChoices = [];
        $labels = $this->datumRepository->findAllLabelsInCollection($entity, DatumTypeEnum::TEXT_TYPES);
        foreach ($labels as $label) {
            $itemsListColumnsChoices[$label['label']] = $label['label'];
        }

        // Move already selected columns to the top of the array
        $alreadySelectedColumns = [];
        if ($entity->getItemsListColumns()) {
            $alreadySelectedColumns = array_reverse($entity->getItemsListColumns());
        }

        foreach ($alreadySelectedColumns as $alreadySelectedColumn) {
            unset($itemsListColumnsChoices[$alreadySelectedColumn]);
            array_unshift($itemsListColumnsChoices, [$alreadySelectedColumn => $alreadySelectedColumn]);
        }

        $builder
            ->add('title', TextType::class, [
                'attr' => ['length' => 255],
                'required' => true,
            ])
            ->add('childrenTitle', TextType::class, [
                'attr' => ['length' => 255],
                'required' => false,
            ])
            ->add('itemsTitle', TextType::class, [
                'attr' => ['length' => 255],
                'required' => false,
            ])
            ->add('visibility', ChoiceType::class, [
                'choices' => array_flip(VisibilityEnum::getVisibilityLabels()),
                'required' => true,
            ])
            ->add('itemsDefaultTemplate', EntityType::class, [
                'class' => Template::class,
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'empty_data' => '',
                'required' => false,
            ])
            ->add('childrenDisplayMode', ChoiceType::class, [
                'choices' => array_flip(DisplayModeEnum::getDisplayModeLabels()),
                'required' => true,
            ])
            ->add('itemsDisplayMode', ChoiceType::class, [
                'choices' => array_flip(DisplayModeEnum::getDisplayModeLabels()),
                'required' => true,
            ])
            ->add('itemsSortingProperty', ChoiceType::class, [
                'choices' => $itemsSortingChoices,
                'required' => true,
            ])
            ->add('itemsListColumns', ChoiceType::class, [
                'choices' => $itemsListColumnsChoices,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('itemsListShowVisibility', CheckboxType::class, [
                'required' => false,
            ])
            ->add('itemsListShowActions', CheckboxType::class, [
                'required' => false,
            ])
            ->add('itemsSortingDirection', ChoiceType::class, [
                'choices' => array_flip(SortingDirectionEnum::getSortingDirectionLabels()),
                'required' => true,
            ])
            ->add('parent', EntityType::class, [
                'class' => Collection::class,
                'choice_label' => 'title',
                'choices' => $this->collectionRepository->findAllExcludingItself($entity),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'empty_data' => '',
                'required' => false,
            ])
            ->add('data', SymfonyCollectionType::class, [
                'entry_type' => DatumType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add(
                $builder->create('file', TextType::class, [
                    'required' => false,
                    'label' => false,
                    'model_transformer' => $this->base64ToImageTransformer,
                ])
            )
        ;

        if ($this->featureChecker->isFeatureEnabled('templates')) {
            $builder->add('template', EntityType::class, [
                'class' => Template::class,
                'choice_label' => 'name',
                'choices' => $this->templateRepository->findAll(),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'required' => false,
                'mapped' => false,
            ]);
        }

        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (FormEvent $event) use ($labels): void {
            $collection = $event->getData();
            $found = false;
            foreach ($labels as $label) {
                if ($label['label'] === $collection->getItemsSortingProperty()) {
                    $collection->setItemsSortingType($label['type']);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $collection->setItemsSortingType(null);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            if (isset($event->getData()['itemsListColumns'])) {
                $this->preSubmitItemsListColumns = $event->getData()['itemsListColumns'];
            }
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            $data->setItemsListColumns($this->preSubmitItemsListColumns);

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collection::class,
        ]);
    }
}
