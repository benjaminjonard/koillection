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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
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

        $itemsDisplayModeListColumnsChoices = [];
        $labels = $this->datumRepository->findAllLabelsInCollection($entity, DatumTypeEnum::TEXT_TYPES);
        foreach ($labels as $label) {
            $itemsDisplayModeListColumnsChoices[$label['label']] = $label['label'];
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
            ->add('itemsDisplayMode', ChoiceType::class, [
                'choices' => array_flip(DisplayModeEnum::getDisplayModeLabels()),
                'required' => true,
            ])
            ->add('itemsSortingProperty', ChoiceType::class, [
                'choices' => $itemsSortingChoices,
                'required' => true,
            ])
            ->add('itemsDisplayModeListColumns', ChoiceType::class, [
                'choices' => $itemsDisplayModeListColumnsChoices,
                'multiple' => true,
                'expanded' => true,
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

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($labels) {
            $collection = $event->getData();

            $found = false;
            foreach ($labels as $label) {
                if ($label['label'] === $collection->getItemsSortingProperty()) {
                    $collection->setItemsSortingType($label['type']);
                    $found = true;
                    break;
                }
            }

            if (false === $found) {
                $collection->setItemsSortingType(null);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collection::class,
        ]);
    }
}
