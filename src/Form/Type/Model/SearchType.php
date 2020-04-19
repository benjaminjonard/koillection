<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Model\Search\Search;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SearchType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;

    /**
     * SearchType constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage =$tokenStorage;
    }

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
            ->add('createdAt', DateType::class, [
                'label' => false,
                'required' => false,
                'html5' => false,
                'widget' => 'single_text',
                'format' => $this->tokenStorage->getToken()->getUser()->getDateFormatForForm()
            ])
            ->add('searchInItems', CheckboxType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('searchInCollections', CheckboxType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('searchInTags', CheckboxType::class, [
                'label' => false,
                'required' => false
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'csrf_protection' => false,

        ]);
    }
}
