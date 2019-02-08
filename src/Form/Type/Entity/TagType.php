<?php

namespace App\Form\Type\Entity;

use App\Entity\Tag;
use App\Enum\VisibilityEnum;
use App\Form\DataTransformer\FileToMediumTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagType
 *
 * @package App\Form\Type\Entity
 */
class TagType extends AbstractType
{
    /**
     * @var FileToMediumTransformer
     */
    private $fileToMediumTransformer;

    /**
     * DatumType constructor.
     * @param FileToMediumTransformer $fileToMediumTransformer
     */
    public function __construct(FileToMediumTransformer $fileToMediumTransformer)
    {
        $this->fileToMediumTransformer = $fileToMediumTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'required' => true,
                'label' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('visibility', ChoiceType::class, [
                'choices' => array_flip(VisibilityEnum::getVisibilityLabels()),
                'required' => false,
            ])
            ->add(
                $builder->create('image', FileType::class, [
                    'required' => false,
                    'label' => false,
                ])->addModelTransformer($this->fileToMediumTransformer)
            )
            ->add('submit', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class
        ]);
    }
}
