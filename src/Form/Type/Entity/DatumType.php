<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Datum;
use App\Form\DataTransformer\FileToMediumTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DatumType
 *
 * @package App\Form\Type\Entity
 */
class DatumType extends AbstractType
{
    /**
     * @var FileToMediumTransformer
     */
    private FileToMediumTransformer $fileToMediumTransformer;

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
            ->add('type', TextType::class, [
                'required' => true,
            ])
            ->add('label', TextType::class, [
                'required' => false,
            ])
            ->add('value', TextType::class, [
                'required' => false,
            ])
            ->add(
                $builder->create('image', FileType::class, [
                    'required' => false,
                    'label' => false,
                ])->addModelTransformer($this->fileToMediumTransformer)
            )
            ->add('position', TextType::class, [
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
            'data_class' => Datum::class
        ]);
    }
}
