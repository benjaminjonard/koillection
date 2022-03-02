<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Model\Search\Search;
use App\Service\FeatureChecker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SearchType extends AbstractType
{
    public function __construct(
        private Security $security,
        private FeatureChecker $featureChecker
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('term', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('createdAt', DateType::class, [
                'label' => false,
                'required' => false,
                'html5' => false,
                'widget' => 'single_text',
                'format' => $this->security->getUser()->getDateFormatForForm(),
            ])
            ->add('searchInItems', CheckboxType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('searchInCollections', CheckboxType::class, [
                'label' => false,
                'required' => false,
            ])
        ;

        if ($this->featureChecker->isFeatureEnabled('tags')) {
            $builder->add('searchInTags', CheckboxType::class, [
                'label' => false,
                'required' => false,
            ]);
        }

        if ($this->featureChecker->isFeatureEnabled('albums')) {
            $builder->add('searchInAlbums', CheckboxType::class, [
                'label' => false,
                'required' => false,
            ]);
        }

        if ($this->featureChecker->isFeatureEnabled('wishlists')) {
            $builder->add('searchInWishlists', CheckboxType::class, [
                'label' => false,
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'csrf_protection' => false,
        ]);
    }
}
