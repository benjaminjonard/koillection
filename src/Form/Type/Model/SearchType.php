<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Model\Search;
use App\Service\DateFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class SearchType
 *
 * @package App\Form\Type\Model
 */
class SearchType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var DateFormatter
     */
    private $dateFormatter;

    /**
     * SearchType constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param DateFormatter $dateFormatter
     */
    public function __construct(TokenStorageInterface $tokenStorage, DateFormatter $dateFormatter)
    {
        $this->tokenStorage =$tokenStorage;
        $this->dateFormatter = $dateFormatter;
    }

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
                'html5' => false,
                'widget' => 'single_text',
                'format' => $this->dateFormatter->guessForForm(),
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
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
        ]);
    }
}
