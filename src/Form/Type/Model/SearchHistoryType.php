<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Enum\HistoryFilterEnum;
use App\Model\Search\SearchHistory;
use App\Service\FeatureChecker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchHistoryType extends AbstractType
{
    public function __construct(
        private FeatureChecker $featureChecker
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $types = [];
        $types[HistoryFilterEnum::FILTER_CLASS_COLLECTION] = HistoryFilterEnum::getLabel(HistoryFilterEnum::FILTER_CLASS_COLLECTION);
        $types[HistoryFilterEnum::FILTER_CLASS_ITEM] = HistoryFilterEnum::getLabel(HistoryFilterEnum::FILTER_CLASS_ITEM);
        if ($this->featureChecker->isFeatureEnabled('tags')) {
            $types[HistoryFilterEnum::FILTER_CLASS_TAG] = HistoryFilterEnum::getLabel(HistoryFilterEnum::FILTER_CLASS_TAG);
        }
        if ($this->featureChecker->isFeatureEnabled('albums')) {
            $types[HistoryFilterEnum::FILTER_CLASS_ALBUM] = HistoryFilterEnum::getLabel(HistoryFilterEnum::FILTER_CLASS_ALBUM);
        }
        if ($this->featureChecker->isFeatureEnabled('wishlists')) {
            $types[HistoryFilterEnum::FILTER_CLASS_WISHLIST] = HistoryFilterEnum::getLabel(HistoryFilterEnum::FILTER_CLASS_WISHLIST);
        }

        $builder
            ->add('term', TextType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('classes', ChoiceType::class, [
                'choices' => array_flip($types),
                'label' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('types', ChoiceType::class, [
                'choices' => array_flip(HistoryFilterEnum::TYPES_TRANS_KEYS),
                'label' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchHistory::class,
            'csrf_protection' => false
        ]);
    }
}
