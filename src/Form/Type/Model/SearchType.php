<?php

namespace App\Form\Type\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SearchType
 *
 * @package App\Form\Type\Model
 */
class SearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $search = $builder->getData();

        $builder
            ->add('search', TextType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('createdAt', DateType::class, [
                'label' => false,
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd'
            ])
            ->add('searchInItems', CheckboxType::class, [
                'label' => false,
                'required' => false,
                'data' => $search->getSearchInItems()
            ])
            ->add('searchInCollections', CheckboxType::class, [
                'label' => false,
                'required' => false,
                'data' => $search->getSearchInCollections()
            ])
            ->add('searchInTags', CheckboxType::class, [
                'label' => false,
                'required' => false,
                'data' => $search->getSearchInTags()
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Model\Search',
        ]);
    }
}
