<?php

namespace App\Form\Type\Model;

use App\Form\DataTransformer\JsonToTagTransformer;
use App\Model\BatchTagger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BatchTaggerType
 *
 * @package App\Form\Type\Model
 */
class BatchTaggerType extends AbstractType
{
    /**
     * @var JsonToTagTransformer
     */
    private $jsonToTagTransformer;

    /**
     * BatchTaggerType constructor.
     * @param JsonToTagTransformer $jsonToTagTransformer
     */
    public function __construct(JsonToTagTransformer $jsonToTagTransformer)
    {
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
