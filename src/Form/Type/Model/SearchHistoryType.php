<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Enum\HistoryFilterEnum;
use App\Model\Search\SearchHistory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchHistoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('term', TextType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('classes', ChoiceType::class, [
                'choices' => array_flip(HistoryFilterEnum::CLASS_TRANS_KEYS),
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchHistory::class,
            'csrf_protection' => false
        ]);
    }
}
