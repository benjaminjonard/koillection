<?php

namespace App\Form\Type\Model;

use App\Entity\Tag;
use App\Form\DataTransformer\JsonToTagTransformer;
use App\Model\BatchTagger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class BatchTaggerType
 *
 * @package App\Form\Type\Model
 */
class BatchTaggerType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var JsonToTagTransformer
     */
    private $jsonToTagTransformer;

    /**
     * BatchTaggerType constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param JsonToTagTransformer $jsonToTagTransformer
     */
    public function __construct(TokenStorageInterface $tokenStorage, JsonToTagTransformer $jsonToTagTransformer)
    {
        $this->tokenStorage = $tokenStorage;
        $this->jsonToTagTransformer = $jsonToTagTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder->create('tags', TextType::class, [
                    'required' => true,
                ])->addModelTransformer($this->jsonToTagTransformer)
            )
            ->add('recursive', CheckboxType::class, [
                'label' => false,
                'required' => false,
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
            'data_class' => BatchTagger::class,
        ]);
    }
}
