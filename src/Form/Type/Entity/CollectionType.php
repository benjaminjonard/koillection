<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Collection;
use App\Enum\VisibilityEnum;
use App\Form\DataTransformer\Base64ToImageTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var Base64ToImageTransformer
     */
    private Base64ToImageTransformer $base64ToImageTransformer;

    /**
     * CollectionType constructor.
     * @param Base64ToImageTransformer $base64ToImageTransformer
     * @param EntityManagerInterface $em
     */
    public function __construct(Base64ToImageTransformer $base64ToImageTransformer, EntityManagerInterface $em)
    {
        $this->base64ToImageTransformer = $base64ToImageTransformer;
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();

        $builder
            ->add('title', TextType::class, [
                'attr' => ['length' => 255],
                'required' => true,
            ])
            ->add('childrenTitle', TextType::class, [
                'attr' => ['length' => 255],
                'required' => false
            ])
            ->add('itemsTitle', TextType::class, [
                'attr' => ['length' => 255],
                'required' => false
            ])
            ->add('visibility', ChoiceType::class, [
                'choices' => \array_flip(VisibilityEnum::getVisibilityLabels()),
                'required' => true,
            ])
            ->add('parent', EntityType::class, [
                'class' => Collection::class,
                'choice_label' => 'title',
                'choices' => $this->em->getRepository(Collection::class)->findAllExcludingItself($entity),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'empty_data' => '',
                'required' => false,
            ])
            ->add(
                $builder->create('file', TextType::class, [
                    'required' => false,
                    'label' => false,
                ])->addModelTransformer($this->base64ToImageTransformer)
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Collection::class
        ]);
    }
}
